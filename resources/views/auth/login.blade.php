<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sanjaya - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1E40AF',
                        accent: '#F59E0B',
                        success: '#10B981',
                        danger: '#EF4444',
                        dark: '#1F2937',
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'slide-in': 'slideIn 0.5s ease-out',
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'bounce-in': 'bounceIn 0.8s ease-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            },
                        },
                        slideIn: {
                            '0%': {
                                transform: 'translateX(-100%)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateX(0)',
                                opacity: '1'
                            },
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        },
                        bounceIn: {
                            '0%': {
                                transform: 'scale(0.3)',
                                opacity: '0'
                            },
                            '50%': {
                                transform: 'scale(1.05)'
                            },
                            '70%': {
                                transform: 'scale(0.9)'
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            },
                        },
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        }

        .glass-effect {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-group {
            position: relative;
        }

        .input-group input {
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2);
        }

        .btn-login {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.3);
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 40%;
            left: 80%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: 1s;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Login Container -->
    <div class="w-full max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">

            <!-- Left Side - Branding -->
            <div class="text-center lg:text-left animate-slide-in">
                <div class="mb-8">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl mb-6 animate-bounce-in">
                        <i class="fas fa-cash-register text-white text-3xl"></i>
                    </div>
                    <h1 class="text-4xl lg:text-5xl font-bold text-white mb-4">
                        POS Sanjaya
                    </h1>
                    <p class="text-xl text-white/80 mb-6">
                        Sistem Point of Sales Modern
                    </p>
                    <p class="text-white/60 leading-relaxed">
                        Kelola bisnis restoran Anda dengan mudah menggunakan sistem POS yang terintegrasi.
                        Pantau penjualan, kelola stok, dan analisa performa bisnis dalam satu platform.
                    </p>
                </div>

                <!-- Features -->
                <div class="hidden lg:block space-y-4">
                    <div class="flex items-center space-x-3 text-white/80">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-sm"></i>
                        </div>
                        <span>Dashboard Analytics Real-time</span>
                    </div>
                    <div class="flex items-center space-x-3 text-white/80">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-boxes text-sm"></i>
                        </div>
                        <span>Manajemen Inventory Lengkap</span>
                    </div>
                    <div class="flex items-center space-x-3 text-white/80">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-print text-sm"></i>
                        </div>
                        <span>Cetak Struk Bluetooth</span>
                    </div>
                    <div class="flex items-center space-x-3 text-white/80">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-sm"></i>
                        </div>
                        <span>Responsive Mobile Friendly</span>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="animate-fade-in">
                <div class="glass-effect rounded-3xl p-8 lg:p-10 shadow-2xl">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang</h2>
                        <p class="text-white/70">Masuk ke akun Anda untuk melanjutkan</p>
                    </div>

                    @if (session('error'))
                        <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg mb-6 animate-fade-in">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ session('error') }}</span>
                                <button onclick="this.parentElement.parentElement.remove()"
                                    class="ml-4 text-white/80 hover:text-white">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg mb-6 animate-fade-in">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-exclamation-circle"></i>
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button onclick="this.parentElement.parentElement.remove()"
                                    class="ml-4 text-white/80 hover:text-white">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form id="loginForm" class="space-y-6" method="POST" action="{{ route('login') }}">
                        @csrf
                        <!-- Username Field -->
                        <div class="input-group">
                            <label class="block text-sm font-medium text-white/80 mb-2">
                                <i class="fas fa-user mr-2"></i>Username
                            </label>
                            <input type="text" id="username" name="name"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-300/50 focus:border-transparent"
                                placeholder="Masukkan username Anda" required>
                        </div>

                        <!-- Password Field -->
                        <div class="input-group">
                            <label class="block text-sm font-medium text-white/80 mb-2">
                                <i class="fas fa-lock mr-2"></i>Password
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-green-300/50 focus:border-transparent pr-12"
                                    placeholder="Masukkan password Anda" required>
                                <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white/80">
                                    <i id="passwordIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" class="sr-only">
                                <div class="relative">
                                    <div class="w-4 h-4 bg-white/20 border border-white/30 rounded"></div>
                                    <div
                                        class="absolute inset-0 w-4 h-4 bg-white rounded opacity-0 transform scale-0 transition-all duration-200">
                                    </div>
                                </div>
                                <span class="ml-2 text-sm text-white/70">Ingat saya</span>
                            </label>
                            <button type="button" class="text-sm text-white/70 hover:text-white/90 transition-colors">
                                Lupa password?
                            </button>
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                            class="btn-login w-full py-3 px-4 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center space-x-2">
                            <span>Masuk</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

   
    <!-- Error Notification -->
    <div id="errorNotification" class="fixed top-4 right-4 z-50 hidden"></div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Show error message
        function showErrorMessage(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg animate-fade-in';
            errorDiv.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white/80 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            const container = document.getElementById('errorNotification');
            container.innerHTML = '';
            container.appendChild(errorDiv);
            container.classList.remove('hidden');

            setTimeout(() => {
                if (errorDiv.parentElement) {
                    errorDiv.remove();
                    container.classList.add('hidden');
                }
            }, 5000);
        }

        // Quick login function
        function quickLogin(role) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.remove('hidden');

            // Auto fill form based on role
            if (role === 'manajemen') {
                document.getElementById('username').value = 'manajemen';
                document.getElementById('password').value = 'manajemen123';
            } else if (role === 'kasir') {
                document.getElementById('username').value = 'kasir';
                document.getElementById('password').value = 'kasir123';
            }

            // Submit form after a brief delay
            setTimeout(() => {
                document.getElementById('loginForm').dispatchEvent(new Event('submit'));
            }, 500);
        }

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const username = document.getElementById('username').value.toLowerCase().trim();
                    const password = document.getElementById('password').value;
                    const loadingOverlay = document.getElementById('loadingOverlay');

                    // Show loading
                    loadingOverlay.classList.remove('hidden');



                    // Add floating animation to shapes
                    document.addEventListener('DOMContentLoaded', function() {
                        const shapes = document.querySelectorAll('.shape');
                        shapes.forEach((shape, index) => {
                            const randomDelay = Math.random() * 2;
                            const randomDuration = 4 + Math.random() * 4;
                            shape.style.animationDelay = `${randomDelay}s`;
                            shape.style.animationDuration = `${randomDuration}s`;
                        });
                    });

                    // Add input focus effects
                    document.querySelectorAll('input').forEach(input => {
                        input.addEventListener('focus', function() {
                            this.parentElement.classList.add('transform', 'scale-105');
                        });

                        input.addEventListener('blur', function() {
                            this.parentElement.classList.remove('transform', 'scale-105');
                        });
                    });
    </script>
</body>

</html>
