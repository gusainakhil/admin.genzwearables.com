<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegisterOtpMail;
use App\Models\CompanyDetail;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => ['required', 'confirmed', Password::min(6)],
            'otp' => 'nullable|digits:6',
            'email_otp' => 'nullable|digits:6',
            'sms_otp' => 'nullable|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $otpCacheKey = $this->otpCacheKey($validated['email'], $validated['phone']);
        $emailOtpInput = (string) ($validated['email_otp'] ?? $validated['otp'] ?? '');
        $smsOtpInput = (string) ($validated['sms_otp'] ?? $validated['otp'] ?? '');

        if (blank($emailOtpInput) && blank($smsOtpInput)) {
            $emailOtp = (string) random_int(100000, 999999);
            $smsOtp = (string) random_int(100000, 999999);

            Cache::put($otpCacheKey, [
                'email_otp_hash' => Hash::make($emailOtp),
                'sms_otp_hash' => Hash::make($smsOtp),
            ], now()->addMinutes(10));

            try {
                $emailDelivery = $this->sendRegistrationOtpEmail($validated['email'], $emailOtp);
            } catch (\Throwable $exception) {
                $emailDelivery = [
                    'channel' => 'email',
                    'sent' => false,
                    'reason' => app()->hasDebugModeEnabled()
                        ? $exception->getMessage()
                        : 'Email delivery failed.',
                ];
            }

            try {
                $smsDelivery = $this->sendRegistrationOtpSms($validated['phone'], $smsOtp);
            } catch (\Throwable $exception) {
                $smsDelivery = [
                    'channel' => 'sms',
                    'sent' => false,
                    'reason' => app()->hasDebugModeEnabled()
                        ? $exception->getMessage()
                        : 'SMS delivery failed.',
                ];
            }

            if (! $emailDelivery['sent'] && ! $smsDelivery['sent'] && $this->canBypassOtpDelivery()) {
                return response()->json([
                    'status' => true,
                    'message' => 'OTP generated in debug bypass mode. Complete provider setup for real delivery.',
                    'data' => [
                        'otp_required' => true,
                        'email_otp_required' => true,
                        'sms_otp_required' => true,
                        'otp_expires_in_seconds' => 600,
                        'otp_preview' => [
                            'email_otp' => $emailOtp,
                            'sms_otp' => $smsOtp,
                        ],
                        'bypass_mode' => true,
                        'delivery' => [
                            'email' => $this->formatDeliveryStatus($emailDelivery),
                            'sms' => $this->formatDeliveryStatus($smsDelivery),
                        ],
                        'delivery_note' => 'sent=true means request submitted to provider. Final inbox or handset delivery must be verified in provider logs.',
                    ],
                ]);
            }

            if (! $emailDelivery['sent'] && ! $smsDelivery['sent']) {
                Cache::forget($otpCacheKey);

                $responseData = [
                    'otp_required' => true,
                    'email_otp_required' => true,
                    'sms_otp_required' => true,
                    'otp_expires_in_seconds' => 600,
                    'delivery' => [
                        'email' => $this->formatDeliveryStatus($emailDelivery),
                        'sms' => $this->formatDeliveryStatus($smsDelivery),
                    ],
                    'delivery_note' => 'sent=true means request submitted to provider. Final inbox or handset delivery must be verified in provider logs.',
                ];

                if ($this->canBypassOtpDelivery()) {
                    $responseData['otp_preview'] = [
                        'email_otp' => $emailOtp,
                        'sms_otp' => $smsOtp,
                    ];
                }

                return response()->json([
                    'status' => false,
                    'message' => 'OTP could not be delivered on email or SMS. Please check configuration.',
                    'data' => $responseData,
                ], 422);
            }

            $responseData = [
                'otp_required' => true,
                'email_otp_required' => true,
                'sms_otp_required' => true,
                'otp_expires_in_seconds' => 600,
                'delivery' => [
                    'email' => $this->formatDeliveryStatus($emailDelivery),
                    'sms' => $this->formatDeliveryStatus($smsDelivery),
                ],
                'delivery_note' => 'sent=true means request submitted to provider. Final inbox or handset delivery must be verified in provider logs.',
            ];

            if ($this->canBypassOtpDelivery()) {
                $responseData['otp_preview'] = [
                    'email_otp' => $emailOtp,
                    'sms_otp' => $smsOtp,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => $emailDelivery['sent'] && $smsDelivery['sent']
                    ? 'OTP sent successfully to email and mobile number'
                    : 'OTP sent partially. Please verify delivery channels.',
                'data' => $responseData,
            ]);
        }

        $cachedOtpData = Cache::get($otpCacheKey);

        if (
            ! is_array($cachedOtpData)
            || blank($cachedOtpData['email_otp_hash'] ?? null)
            || blank($cachedOtpData['sms_otp_hash'] ?? null)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired or not found. Please request a new OTP.',
            ], 422);
        }

        if (blank($emailOtpInput) || blank($smsOtpInput)) {
            return response()->json([
                'status' => false,
                'message' => 'Both email_otp and sms_otp are required for verification.',
            ], 422);
        }

        if (
            ! Hash::check($emailOtpInput, (string) ($cachedOtpData['email_otp_hash'] ?? ''))
            || ! Hash::check($smsOtpInput, (string) ($cachedOtpData['sms_otp_hash'] ?? ''))
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
            ], 422);
        }

        Cache::forget($otpCacheKey);
 
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    private function otpCacheKey(string $email, string $phone): string
    {
        return 'register_otp:'.sha1(strtolower(trim($email)).'|'.trim($phone));
    }

    /**
     * @return array{channel:string,sent:bool,reason:?string}
     */
    private function sendRegistrationOtpEmail(string $email, string $otp): array
    {
        $this->applyOtpMailConfigurationFromSettings();

        $mailer = (string) config('mail.default');

        if (in_array($mailer, ['log', 'array'], true)) {
            return [
                'channel' => 'email',
                'sent' => false,
                'reason' => 'MAIL_MAILER is set to '.$mailer.'. Configure SMTP for real email delivery.',
            ];
        }

        $companyDetail = CompanyDetail::query()->first();

        $brandName = (string) (config('mail.from.name') ?: $companyDetail?->brand_name ?: config('app.name'));
        $smtpUsername = (string) config('mail.mailers.smtp.username');
        $fromAddress = (string) (config('mail.from.address') ?: $smtpUsername ?: $companyDetail?->email_secondary);

        if (blank($fromAddress)) {
            return [
                'channel' => 'email',
                'sent' => false,
                'reason' => 'Sender email is missing. Set company_details.email_secondary or MAIL_FROM_ADDRESS.',
            ];
        }

        $smtpHost = (string) config('mail.mailers.smtp.host');
        $smtpPort = (int) config('mail.mailers.smtp.port');
        $smtpScheme = config('mail.mailers.smtp.scheme');

        $transports = [
            [
                'host' => $smtpHost,
                'port' => $smtpPort,
                'scheme' => $smtpScheme,
                'label' => 'configured',
            ],
            [
                'host' => $smtpHost,
                'port' => 587,
                'scheme' => 'smtp',
                'label' => 'fallback-587',
            ],
            [
                'host' => $smtpHost,
                'port' => 465,
                'scheme' => 'smtps',
                'label' => 'fallback-465',
            ],
        ];

        if (strtolower(trim($smtpHost)) === 'smtp.gmail.com') {
            $transports[] = [
                'host' => $smtpHost,
                'port' => 25,
                'scheme' => 'smtp',
                'label' => 'gmail-25',
            ];
        }

        $transports = collect($transports)
            ->unique(fn (array $transport) => $transport['host'].'|'.$transport['port'].'|'.(string) $transport['scheme'])
            ->values()
            ->all();

        $attemptReasons = [];

        foreach ($transports as $transport) {
            config([
                'mail.mailers.smtp.host' => $transport['host'],
                'mail.mailers.smtp.port' => $transport['port'],
                'mail.mailers.smtp.scheme' => $transport['scheme'],
            ]);

            app('mail.manager')->forgetMailers();

            try {
                Mail::to($email)->send(new RegisterOtpMail($brandName, $otp, $fromAddress));

                return [
                    'channel' => 'email',
                    'sent' => true,
                    'reason' => null,
                ];
            } catch (\Throwable $exception) {
                $attemptReasons[] = $transport['label'].' failed: '.Str::limit($exception->getMessage(), 140);
            }
        }

        try {
            app('mail.manager')->forgetMailers();
            Mail::mailer('sendmail')->to($email)->send(new RegisterOtpMail($brandName, $otp, $fromAddress));

            return [
                'channel' => 'email',
                'sent' => true,
                'reason' => null,
            ];
        } catch (\Throwable $exception) {
            $attemptReasons[] = 'sendmail failed: '.Str::limit($exception->getMessage(), 140);
        }

        return [
            'channel' => 'email',
            'sent' => false,
            'reason' => filled($attemptReasons)
                ? Str::limit(implode(' | ', $attemptReasons), 450)
                : 'Email delivery failed for all configured SMTP transports.',
        ];
    }

    private function applyOtpMailConfigurationFromSettings(): void
    {
        $smtpHost = trim((string) Setting::get('smtp_host', ''));
        $smtpPort = trim((string) Setting::get('smtp_port', ''));
        $smtpUsername = trim((string) Setting::get('smtp_username', ''));
        $smtpPassword = (string) Setting::get('smtp_password', '');
        $smtpEncryption = trim((string) Setting::get('smtp_encryption', ''));
        $smtpFromAddress = trim((string) Setting::get('smtp_from_address', ''));
        $smtpFromName = trim((string) Setting::get('smtp_from_name', ''));

        $smtpPortInt = (int) $smtpPort;

        $smtpScheme = match (true) {
            $smtpPortInt === 465 => 'smtps',
            $smtpPortInt === 587 => 'smtp',
            $smtpEncryption === 'ssl' => 'smtps',
            $smtpEncryption === 'tls' => 'smtp',
            default => null,
        };

        if (blank($smtpHost) || blank($smtpPort)) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $smtpHost,
            'mail.mailers.smtp.port' => $smtpPortInt,
            'mail.mailers.smtp.username' => blank($smtpUsername) ? null : $smtpUsername,
            'mail.mailers.smtp.password' => blank($smtpPassword) ? null : $smtpPassword,
            'mail.mailers.smtp.scheme' => $smtpScheme,
            'mail.mailers.smtp.timeout' => 8,
            'mail.from.address' => blank($smtpFromAddress) ? config('mail.from.address') : $smtpFromAddress,
            'mail.from.name' => blank($smtpFromName) ? config('mail.from.name') : $smtpFromName,
        ]);
    }

    /**
     * @return array{channel:string,sent:bool,reason:?string}
     */
    private function sendRegistrationOtpSms(string $phone, string $otp): array
    {
        $authkeyApiKey = (string) Setting::get('authkey_api_key', '');
        $authkeySenderId = trim((string) Setting::get('authkey_sender_id', ''));
        $authkeyTemplateId = trim((string) Setting::get('authkey_template_id', ''));

        if (blank($authkeyApiKey)) {
            return [
                'channel' => 'sms',
                'sent' => false,
                'reason' => 'Authkey API key is missing in settings.',
            ];
        }

        $normalizedPhone = preg_replace('/\D+/', '', $phone);

        if (blank($normalizedPhone)) {
            return [
                'channel' => 'sms',
                'sent' => false,
                'reason' => 'Phone number is invalid.',
            ];
        }

        $mobileCandidates = collect([
            $normalizedPhone,
            '91'.$normalizedPhone,
            '+91'.$normalizedPhone,
        ])->unique()->values();

        $failureReasons = [];

        foreach ($mobileCandidates as $mobileCandidate) {
            $requestVariants = [
                [
                    'authkey' => $authkeyApiKey,
                    'mobile' => $mobileCandidate,
                    'country_code' => 91,
                    'otp' => $otp,
                ],
                [
                    'authkey' => $authkeyApiKey,
                    'mobile' => $mobileCandidate,
                    'country_code' => 91,
                ],
            ];

            if (filled($authkeySenderId)) {
                $requestVariants[0]['sid'] = $authkeySenderId;
                $requestVariants[1]['sid'] = $authkeySenderId;
            }

            if (filled($authkeyTemplateId)) {
                $requestVariants[0]['template_id'] = $authkeyTemplateId;
                $requestVariants[1]['template_id'] = $authkeyTemplateId;
            }

            foreach ($requestVariants as $variantIndex => $queryParams) {
                $response = Http::connectTimeout(3)
                    ->timeout(5)
                    ->acceptJson()
                    ->get('https://api.authkey.io/request', $queryParams);

                $delivery = $this->parseAuthkeyResponse($response);

                if ($delivery['sent']) {
                    return [
                        'channel' => 'sms',
                        'sent' => true,
                        'reason' => null,
                    ];
                }

                if (filled($delivery['reason'])) {
                    $attempt = $variantIndex === 0 ? 'with-otp' : 'without-otp';
                    $failureReasons[] = 'mobile '.$mobileCandidate.' ('.$attempt.'): '.$delivery['reason'];
                }
            }
        }

        return [
            'channel' => 'sms',
            'sent' => false,
            'reason' => filled($failureReasons)
                ? Str::limit(implode(' | ', array_unique($failureReasons)), 450)
                : 'Authkey response could not be verified as successful.',
        ];
    }

    /**
     * @return array{sent:bool,reason:?string}
     */
    private function parseAuthkeyResponse($response): array
    {
        if (! $response->successful()) {
            return [
                'sent' => false,
                'reason' => 'HTTP '.$response->status().': '.Str::limit(trim($response->body()), 180),
            ];
        }

        $payload = $response->json();

        if (is_array($payload)) {
            $statusValue = data_get($payload, 'status', data_get($payload, 'Status', data_get($payload, 'success', data_get($payload, 'Success'))));
            $statusCodeValue = data_get($payload, 'code', data_get($payload, 'Code', data_get($payload, 'statusCode', data_get($payload, 'status_code', data_get($payload, 'StatusCode')))));
            $message = (string) (data_get($payload, 'message')
                ?: data_get($payload, 'Message')
                ?: data_get($payload, 'error')
                ?: data_get($payload, 'Error')
                ?: data_get($payload, 'description')
                ?: data_get($payload, 'Description')
                ?: '');

            $normalizedStatus = strtolower(trim((string) $statusValue));
            $normalizedMessage = strtolower(trim($message));

            if (in_array($normalizedStatus, ['success', 'ok', 'sent', 'true', '1'], true)) {
                return ['sent' => true, 'reason' => null];
            }

            if (in_array((string) $statusCodeValue, ['200', '201'], true)) {
                return ['sent' => true, 'reason' => null];
            }

            if (
                str_contains($normalizedMessage, 'otp sent')
                || str_contains($normalizedMessage, 'sent successfully')
                || str_contains($normalizedMessage, 'submitted successfully')
            ) {
                return ['sent' => true, 'reason' => null];
            }

            if (str_contains($normalizedMessage, 'nothing to do')) {
                return [
                    'sent' => false,
                    'reason' => 'Authkey responded "Nothing to do". Usually sender/template/route parameters are missing or OTP request is blocked for current config.',
                ];
            }

            if (filled(data_get($payload, 'id')) || filled(data_get($payload, 'request_id')) || filled(data_get($payload, 'message_id'))) {
                return ['sent' => true, 'reason' => null];
            }

            return [
                'sent' => false,
                'reason' => filled($message)
                    ? $message
                    : 'Unverified JSON response: '.Str::limit(json_encode($payload), 180),
            ];
        }

        $responseBody = strtolower(trim($response->body()));

        if (str_contains($responseBody, 'success') || str_contains($responseBody, 'sent') || str_contains($responseBody, 'otp')) {
            return ['sent' => true, 'reason' => null];
        }

        return [
            'sent' => false,
            'reason' => 'Unverified response body: '.Str::limit(trim($response->body()), 180),
        ];
    }

    private function canBypassOtpDelivery(): bool
    {
        return (bool) config('app.debug');
    }

    /**
     * @param  array{channel:string,sent:bool,reason:?string}  $delivery
     * @return array{channel:string,sent:bool,submitted_to_provider:bool,delivered_to_inbox:string,reason:?string}
     */
    private function formatDeliveryStatus(array $delivery): array
    {
        $reason = $delivery['reason'] ?? null;

        if (($delivery['sent'] ?? false) && blank($reason)) {
            $reason = 'Submitted to provider. Final delivery is provider-dependent.';
        }

        return [
            'channel' => (string) ($delivery['channel'] ?? ''),
            'sent' => (bool) ($delivery['sent'] ?? false),
            'submitted_to_provider' => (bool) ($delivery['sent'] ?? false),
            'delivered_to_inbox' => 'unknown',
            'reason' => $reason,
        ];
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()?->load('addresses');

        return response()->json([
            'status' => true,
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
