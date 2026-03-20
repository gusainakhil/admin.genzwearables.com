@extends('admin.layout')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="w-full space-y-6">
    
    @if(session('success'))
    <div class="rounded-[28px] border border-emerald-200/60 bg-emerald-50 p-6 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white">
                <i class="fas fa-check"></i>
            </div>
            <p class="font-semibold text-emerald-900">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Payment Gateway Settings -->
    <!-- <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-8">
        <div class="mb-6">
            <h3 class="text-2xl font-semibold text-stone-900 mb-2">Payment Gateway</h3>
            <p class="text-sm text-stone-500">Configure Razorpay payment gateway settings for your store</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf -->

            <!-- Razorpay Status -->
            <!-- <div class="rounded-2xl border border-rose-100/70 bg-stone-50/50 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-semibold text-stone-900 mb-1">Enable Razorpay</label>
                        <p class="text-xs text-stone-500">Turn on/off Razorpay payment gateway</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="razorpay_enabled" value="1" class="peer sr-only" {{ $settings['razorpay_enabled'] == '1' ? 'checked' : '' }}>
                        <div class="peer h-7 w-14 rounded-full bg-stone-300 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:border after:border-stone-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-gradient-to-r peer-checked:from-rose-500 peer-checked:to-amber-500 peer-checked:after:translate-x-7 peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-rose-200/50"></div>
                    </label>
                </div>
            </div> -->

            <!-- Razorpay Key ID -->

            <!-- <div>
                <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                    <i class="fas fa-key text-rose-500 mr-2"></i>Razorpay Key ID
                </label>
                <input 
                    type="text" 
                    name="razorpay_key_id" 
                    value="{{ old('razorpay_key_id', $settings['razorpay_key_id']) }}"
                    placeholder="rzp_test_xxxxxxxxxxxxx"
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >
                @error('razorpay_key_id')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-stone-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Your Razorpay Key ID (starts with rzp_test_ or rzp_live_)
                </p>
            </div> -->

            <!-- Razorpay Key Secret -->
            <!-- <div>
                <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                    <i class="fas fa-lock text-rose-500 mr-2"></i>Razorpay Key Secret
                </label>
                <input 
                    type="password" 
                    name="razorpay_key_secret" 
                    value="{{ old('razorpay_key_secret', $settings['razorpay_key_secret']) }}"
                    placeholder="Enter your Razorpay Key Secret"
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >
                @error('razorpay_key_secret')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-stone-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Keep this secret safe and never share it publicly
                </p>
            </div> -->

            <!-- Info Box -->
            <!-- <div class="rounded-2xl border border-amber-200/60 bg-amber-50/50 p-6">
                <div class="flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-amber-900 mb-2">How to get Razorpay API Keys</h4>
                        <ol class="space-y-1 text-sm text-amber-800">
                            <li>1. Login to your <a href="https://dashboard.razorpay.com/" target="_blank" class="font-semibold underline hover:text-amber-900">Razorpay Dashboard</a></li>
                            <li>2. Navigate to Settings → API Keys</li>
                            <li>3. Generate new keys or use existing test/live keys</li>
                            <li>4. Copy and paste both Key ID and Key Secret here</li>
                        </ol>
                    </div>
                </div>
            </div> -->

            <!-- Action Buttons -->
            <!-- <div class="flex flex-wrap gap-4 pt-4">
                <button 
                    type="submit" 
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-8 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-rose-200/50"
                >
                    <i class="fas fa-save"></i>
                    Save Settings
                </button>
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-8 py-3 text-sm font-semibold text-stone-600 transition hover:bg-stone-50"
                >
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div> -->

    <!-- OTP Settings (Authkey) -->
    <!-- <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-8">
        <div class="mb-6">
            <h3 class="text-2xl font-semibold text-stone-900 mb-2">OTP Settings</h3>
            <p class="text-sm text-stone-500">Configure Authkey API key for OTP and SMS integration</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                    <i class="fas fa-sms text-rose-500 mr-2"></i>Authkey API Key
                </label>
                <input
                    type="text"
                    name="authkey_api_key"
                    value="{{ old('authkey_api_key', $settings['authkey_api_key']) }}"
                    placeholder="Enter your Authkey API Key"
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >
                @error('authkey_api_key')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-stone-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Add your Authkey API key for SMS OTP and message APIs
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                        <i class="fas fa-broadcast-tower text-rose-500 mr-2"></i>Authkey Sender ID
                    </label>
                    <input
                        type="text"
                        name="authkey_sender_id"
                        value="{{ old('authkey_sender_id', $settings['authkey_sender_id']) }}"
                        placeholder="e.g. GZWOTP"
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                    >
                    @error('authkey_sender_id')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                        <i class="fas fa-id-card text-rose-500 mr-2"></i>Authkey Template ID
                    </label>
                    <input
                        type="text"
                        name="authkey_template_id"
                        value="{{ old('authkey_template_id', $settings['authkey_template_id']) }}"
                        placeholder="DLT/Template ID"
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                    >
                    @error('authkey_template_id')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-2xl border border-sky-200/60 bg-sky-50/50 p-6">
                <div class="flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500 text-white">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sky-900 mb-2">How to set up Authkey API Key</h4>
                        <ol class="space-y-1 text-sm text-sky-800">
                            <li>1. Open the <a href="https://api.authkey.io/" target="_blank" rel="noopener" class="font-semibold underline hover:text-sky-900">Authkey API portal</a></li>
                            <li>2. Sign in to your Authkey account</li>
                            <li>3. Go to API settings and copy your API Key</li>
                            <li>4. Paste the key here and click Save OTP Settings</li>
                        </ol>
                    </div>
                </div>
            </div> -->

            <!-- <div class="flex flex-wrap gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-8 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-rose-200/50"
                >
                    <i class="fas fa-save"></i>
                    Save OTP Settings
                </button>
            </div>
        </form>
    </div> -->

    <!-- SMTP Email Settings -->
    <!-- <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-8">
        <div class="mb-6">
            <h3 class="text-2xl font-semibold text-stone-900 mb-2">SMTP Email Settings</h3>
            <p class="text-sm text-stone-500">Configure SMTP for OTP email delivery</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-rose-100/70 bg-stone-50/50 p-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                            <i class="fas fa-server text-rose-500 mr-2"></i>SMTP Host
                        </label>
                        <input
                            type="text"
                            name="smtp_host"
                            value="{{ old('smtp_host', $settings['smtp_host']) }}"
                            placeholder="smtp.gmail.com"
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        >
                        @error('smtp_host')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                            <i class="fas fa-plug text-rose-500 mr-2"></i>SMTP Port
                        </label>
                        <input
                            type="number"
                            name="smtp_port"
                            value="{{ old('smtp_port', $settings['smtp_port']) }}"
                            placeholder="587"
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        >
                        @error('smtp_port')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                            <i class="fas fa-user text-rose-500 mr-2"></i>SMTP Username
                        </label>
                        <input
                            type="text"
                            name="smtp_username"
                            value="{{ old('smtp_username', $settings['smtp_username']) }}"
                            placeholder="your-email@example.com"
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        >
                        @error('smtp_username')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                            <i class="fas fa-lock text-rose-500 mr-2"></i>SMTP Password
                        </label>
                        <input
                            type="password"
                            name="smtp_password"
                            placeholder="Leave blank to keep existing password"
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        >
                        @error('smtp_password')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                            <i class="fas fa-shield-alt text-rose-500 mr-2"></i>SMTP Encryption
                        </label>
                        <select
                            name="smtp_encryption"
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        >
                            <option value="">No Encryption</option>
                            <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption']) === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                        @error('smtp_encryption')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                            <i class="fas fa-envelope-open text-rose-500 mr-2"></i>From Email
                        </label>
                        <input
                            type="email"
                            name="smtp_from_address"
                            value="{{ old('smtp_from_address', $settings['smtp_from_address']) }}"
                            placeholder="noreply@yourdomain.com"
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        >
                        @error('smtp_from_address')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                            <i class="fas fa-signature text-rose-500 mr-2"></i>From Name
                        </label>
                        <input
                            type="text"
                            name="smtp_from_name"
                            value="{{ old('smtp_from_name', $settings['smtp_from_name']) }}"
                            placeholder="Your Brand Name"
                            class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        >
                        @error('smtp_from_name')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-sky-200/60 bg-sky-50/50 p-6">
                <div class="flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500 text-white">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-sky-900 mb-2">How to set up SMTP Email</h4>
                        <ol class="space-y-1 text-sm text-sky-800">
                            <li>1. Enter your mail server host (for example: <span class="font-semibold">mail.yourdomain.com</span>)</li>
                            <li>2. Use the correct port and encryption pair: <span class="font-semibold">587 + TLS</span> or <span class="font-semibold">465 + SSL</span></li>
                            <li>3. Add full mailbox username and mailbox password</li>
                            <li>4. Keep From Email same as SMTP mailbox to avoid sender rejection</li>
                            <li>5. Save SMTP Settings and test signup OTP again</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-8 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-rose-200/50"
                >
                    <i class="fas fa-save"></i>
                    Save SMTP Settings
                </button>
            </div>
        </form>
    </div> -->

    <!-- Shipment API Credentials -->
    <!-- <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-8">
        <div class="mb-6">
            <h3 class="text-2xl font-semibold text-stone-900 mb-2">Shipment API (Shiprocket)</h3>
            <p class="text-sm text-stone-500">Configure Shiprocket API credentials for shipment booking and tracking.</p>
        </div>

        <form action="{{ route('admin.settings.shipment-api.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-rose-100/70 bg-stone-50/50 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-semibold text-stone-900 mb-1">Enable Shiprocket</label>
                        <p class="text-xs text-stone-500">Turn on/off shipment API integration</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" class="peer sr-only" {{ old('is_active', $shipmentApiCredential?->is_active) ? 'checked' : '' }}>
                        <div class="peer h-7 w-14 rounded-full bg-stone-300 after:absolute after:left-1 after:top-1 after:h-5 after:w-5 after:rounded-full after:border after:border-stone-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-gradient-to-r peer-checked:from-rose-500 peer-checked:to-amber-500 peer-checked:after:translate-x-7 peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-rose-200/50"></div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                    <i class="fas fa-envelope text-rose-500 mr-2"></i>Shiprocket Email
                </label>
                <input
                    type="email"
                    name="api_email"
                    value="{{ old('api_email', $shipmentApiCredential?->api_email) }}"
                    placeholder="your-shiprocket-email@example.com"
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                    required
                >
                @error('api_email')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                    <i class="fas fa-lock text-rose-500 mr-2"></i>Shiprocket Password
                </label>
                <input
                    type="password"
                    name="api_password"
                    placeholder="{{ $shipmentApiCredential ? 'Leave blank to keep current password' : 'Enter Shiprocket password' }}"
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >
                @error('api_password')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                    <i class="fas fa-key text-rose-500 mr-2"></i>Shiprocket API Token (Optional)
                </label>
                <input
                    type="text"
                    name="api_token"
                    value="{{ old('api_token', $shipmentApiCredential?->api_token) }}"
                    placeholder="Optional token if already generated"
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                >
                @error('api_token')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-8 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-rose-200/50"
                >
                    <i class="fas fa-shipping-fast"></i>
                    Save Shiprocket Credentials
                </button>
            </div>
        </form>

        <form action="{{ route('admin.settings.shipment-api.generate-token') }}" method="POST" class="mt-4">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-8 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-stone-700 transition hover:border-rose-300 hover:text-stone-900 focus:outline-none focus:ring-4 focus:ring-rose-200/50"
            >
                <i class="fas fa-key"></i>
                Generate Shiprocket Token
            </button>
        </form>
    </div> -->

    <!-- Admin Password Settings -->
     <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-8">
        <div class="mb-6">
            <h3 class="text-2xl font-semibold text-stone-900 mb-2">Admin Password</h3>
            <p class="text-sm text-stone-500">Update your own login password securely</p>
        </div>

        <form action="{{ route('admin.settings.password.update') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                    <i class="fas fa-lock text-rose-500 mr-2"></i>Current Password
                </label>
                <input
                    type="password"
                    name="current_password"
                    placeholder="Enter current password"
                    class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                    required
                >
                @error('current_password')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                        <i class="fas fa-key text-rose-500 mr-2"></i>New Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Minimum 8 characters"
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        required
                    >
                    @error('password')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold uppercase tracking-wider text-stone-500 mb-3">
                        <i class="fas fa-check-circle text-rose-500 mr-2"></i>Confirm New Password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Re-enter new password"
                        class="w-full rounded-2xl border border-rose-200/60 bg-white px-5 py-3 text-sm text-stone-800 shadow-sm placeholder:text-stone-400 focus:border-rose-400 focus:outline-none focus:ring-4 focus:ring-rose-100/50"
                        required
                    >
                </div>
            </div>

            <div class="flex flex-wrap gap-4 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-rose-500 to-amber-500 px-8 py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-sm transition hover:from-rose-600 hover:to-amber-600 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-rose-200/50"
                >
                    <i class="fas fa-shield-alt"></i>
                    Update Password
                </button>
            </div>
        </form>
    </div> 

    <!-- API Documentation -->
    <!-- <div class="rounded-[28px] border border-rose-200/60 bg-white shadow-sm p-8">
        <div class="mb-6">
            <h3 class="text-2xl font-semibold text-stone-900 mb-2">
                <i class="fas fa-book text-rose-500 mr-2"></i>
                Integration Guide
            </h3>
            <p class="text-sm text-stone-500">Quick reference for Razorpay integration</p>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-stone-200 bg-stone-50/50 p-6">
                <h4 class="text-sm font-semibold text-stone-900 mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-500 text-xs text-white">1</span>
                    Test Mode vs Live Mode
                </h4>
                <p class="text-sm text-stone-600 mb-3">
                    Use test keys (starting with <code class="rounded bg-stone-200 px-2 py-1 text-xs font-mono">rzp_test_</code>) for development and live keys for production.
                </p>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-stone-50/50 p-6">
                <h4 class="text-sm font-semibold text-stone-900 mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-500 text-xs text-white">2</span>
                    Webhook Setup
                </h4>
                <p class="text-sm text-stone-600 mb-3">
                    Configure webhooks in Razorpay Dashboard to receive payment notifications automatically.
                </p>
                <div class="rounded-xl bg-stone-900 p-4">
                    <code class="block text-xs text-emerald-400 font-mono">{{ url('/api/razorpay/webhook') }}</code>
                </div>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-stone-50/50 p-6">
                <h4 class="text-sm font-semibold text-stone-900 mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-500 text-xs text-white">3</span>
                    Authkey SMS Setup
                </h4>
                <p class="text-sm text-stone-600 mb-3">
                    Keep your Authkey credentials ready for OTP and SMS notifications.
                </p>
                <ul class="space-y-2 text-sm text-stone-600">
                    <li class="flex gap-2">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                        <span>Generate API Key from <a href="https://api.authkey.io/" target="_blank" rel="noopener" class="font-semibold underline hover:text-stone-800">api.authkey.io</a></span>
                    </li>
                    <li class="flex gap-2">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                        <span>Store the key only in admin settings and never expose it publicly</span>
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-stone-50/50 p-6">
                <h4 class="text-sm font-semibold text-stone-900 mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-500 text-xs text-white">4</span>
                    Security Best Practices
                </h4>
                <ul class="space-y-2 text-sm text-stone-600">
                    <li class="flex gap-2">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                        <span>Never expose your Key Secret in frontend code</span>
                    </li>
                    <li class="flex gap-2">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                        <span>Always verify payment signatures on server-side</span>
                    </li>
                    <li class="flex gap-2">
                        <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                        <span>Use environment variables for storing keys</span>
                    </li>
                </ul>
            </div>
        </div>
    </div> -->
</div>
@endsection
