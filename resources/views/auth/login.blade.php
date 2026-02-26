<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ $companyDetail?->brand_name ?? 'GenZ Wearables' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .input-focus {
            @apply transition-all duration-300;
        }
        .input-focus:focus {
            @apply ring-2 ring-purple-500 border-transparent shadow-lg;
        }
        .btn-submit {
            @apply transition-all duration-300 transform hover:scale-105;
        }
        .btn-submit:active {
            @apply scale-95;
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100">
    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-0 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-pink-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-1/2 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <style>
        @keyframes blob {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                @if($companyDetail?->logo)
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white p-2 mb-4 shadow-lg">
                        <img src="{{ asset('storage/' . $companyDetail->logo) }}" alt="{{ $companyDetail->brand_name ?? 'Brand' }} Logo" class="h-full w-full object-contain">
                    </div>
                @else
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full gradient-bg mb-4">
                        <i class="fas fa-shirt text-2xl text-white"></i>
                    </div>
                @endif
                <h1 class="text-4xl font-bold text-white mb-2">{{ $companyDetail?->brand_name ?? 'GenZ Wearables' }}</h1>
                <p class="text-gray-400 text-sm">Admin Dashboard</p>
            </div>

            <!-- Login Card -->
            <div class="bg-gray-900 rounded-2xl login-card p-8 shadow-2xl">
                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-6 bg-red-900/30 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
                            <div>
                                <p class="font-semibold text-sm mb-2">Login Failed</p>
                                <ul class="text-xs space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if(session('status'))
                    <div class="mb-6 bg-green-900/30 border border-green-500/50 text-green-200 px-4 py-3 rounded-lg flex items-start">
                        <i class="fas fa-check-circle mt-0.5 mr-3"></i>
                        <p class="text-sm">{{ session('status') }}</p>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
                            <i class="fas fa-envelope mr-2 text-purple-400"></i>Email Address
                        </label>
                        <div class="relative">
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                value="{{ old('email') }}" 
                                required 
                                placeholder="admin@{{ strtolower(str_replace(' ', '', $companyDetail?->brand_name ?? 'genzwearables')) }}.com"
                                class="input-focus w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-red-400"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2 text-purple-400"></i>Password
                        </label>
                        <div class="relative">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                placeholder="Enter your password"
                                class="input-focus w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none"
                            >
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-400"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="w-4 h-4 bg-gray-800 border border-gray-700 rounded cursor-pointer accent-purple-500"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-400 cursor-pointer hover:text-gray-300 transition">
                            Remember me for 30 days
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="btn-submit w-full py-3 px-4 rounded-lg font-semibold text-white bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 mt-6"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In to Admin Panel
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center">
                    <div class="flex-1 border-t border-gray-700"></div>
                    <span class="px-3 text-gray-500 text-xs">or</span>
                    <div class="flex-1 border-t border-gray-700"></div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-900/30 border border-blue-500/50 rounded-lg p-4">
                    <p class="text-xs text-blue-200 mb-2">
                        <i class="fas fa-info-circle mr-2"></i><strong>Demo Credentials:</strong>
                    </p>
                    <div class="space-y-1 text-xs text-blue-300 font-mono">
                        <p>📧 <span class="select-all">admin@genzwearables.com</span></p>
                        <p>🔑 <span class="select-all">password</span></p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-gray-500 text-xs">
                    {{ $companyDetail?->brand_name ?? 'GenZ Wearables' }} Admin Dashboard &copy; <span id="year"></span>
                </p>
                <p class="text-gray-600 text-xs mt-2">
                    Secure Admin Portal • v1.0
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>