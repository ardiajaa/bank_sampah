<?php
require_once 'functions.php';

if (isLoggedIn()) {
    redirectBasedOnRole();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        
        // Tampilkan loading screen sebelum redirect
        echo '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Loading - Bank Sampah</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
            <style>
                .loading-bg {
                    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
                    animation: gradientBG 8s ease infinite;
                }
                @keyframes gradientBG {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
                .loader {
                    width: 120px;
                    height: 120px;
                    position: relative;
                    animation: rotate 3s ease-in-out infinite;
                }
                .loader-circle {
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    border: 8px solid transparent;
                    border-radius: 50%;
                    animation: loaderCircle 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
                }
                .loader-circle:nth-child(1) {
                    border-top-color: #ffffff;
                    animation-delay: -0.5s;
                }
                .loader-circle:nth-child(2) {
                    border-left-color: rgba(255, 255, 255, 0.7);
                    animation-delay: -1s;
                }
                .loader-circle:nth-child(3) {
                    border-bottom-color: rgba(255, 255, 255, 0.4);
                    animation-delay: -1.5s;
                }
                @keyframes rotate {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                @keyframes loaderCircle {
                    0% { transform: scale(1); opacity: 1; }
                    50% { transform: scale(0.8); opacity: 0.5; }
                    100% { transform: scale(1); opacity: 1; }
                }
                .progress-bar {
                    width: 300px;
                    height: 6px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 3px;
                    overflow: hidden;
                    position: relative;
                }
                .progress-fill {
                    width: 100%;
                    height: 100%;
                    background: white;
                    position: absolute;
                    left: -100%;
                    animation: progress 4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
                }
                @keyframes progress {
                    0% { left: -100%; }
                    100% { left: 0; }
                }
            </style>
            <script>
                setTimeout(function() {
                    window.location.href = "'.($_SESSION['user_role'] === 'admin' ? 'admin_dashboard.php' : 'dashboard.php').'";
                }, 5000); // Redirect setelah 5 detik
            </script>
        </head>
        <body class="loading-bg min-h-screen flex items-center justify-center">
            <div class="text-center space-y-8">
                <div class="animate__animated animate__fadeIn">
                    <div class="loader mx-auto">
                        <div class="loader-circle"></div>
                        <div class="loader-circle"></div>
                        <div class="loader-circle"></div>
                    </div>
                </div>
                <div class="space-y-3 animate__animated animate__fadeInUp animate__delay-1s">
                    <h2 class="text-white text-3xl font-bold">Selamat Datang '.htmlspecialchars($_SESSION['user_name']).'!</h2>
                    <p class="text-gray-200 text-lg">Mengarahkan ke dashboard...</p>
                </div>
                <div class="animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="progress-bar mx-auto">
                        <div class="progress-fill"></div>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        exit();
    } else {
        $_SESSION['login_error'] = 'Email atau password salah!';
        header("Location: login.php");
        exit();
    }
}

// Cek jika ada error dari session
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Hapus error setelah ditampilkan
}

function redirectBasedOnRole() {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .login-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            background-size: 200% 200%;
            animation: gradientBG 10s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-card {
            transition: all 0.5s ease;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
        }
        .input-field {
            transition: all 0.3s ease;
            background: rgba(249, 250, 251, 0.8);
        }
        .input-field:focus {
            background: white;
            transform: scale(1.02);
        }
        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <div class="login-card w-full max-w-md animate__animated animate__fadeInUp">
        <div class="p-8">
            <div class="text-center mb-8">
                <i class="fas fa-recycle text-blue-600 text-8xl mx-auto mb-8 animate__animated animate__bounceIn"></i>
                <h1 class="text-4xl font-bold text-gray-800 mb-2 animate__animated animate__fadeIn">Bank Sampah</h1>
                <p class="text-gray-600 text-lg animate__animated animate__fadeIn">Silahkan Masuk Disini!</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST" class="space-y-6">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="email" id="email" name="email" required 
                               class="input-field w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 relative"
                               placeholder="contoh@email.com">
                    </div>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="password" id="password" name="password" required 
                               class="input-field w-full pl-10 pr-3 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 relative"
                               placeholder="••••••••">
                    </div>
                </div>
                <button type="submit" 
                        class="btn-login w-full text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                </button>
            </form>
            
            <div class="mt-8 text-center">
                <p class="text-gray-600">Belum punya akun? 
                    <a href="register.php" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline animate__animated animate__fadeIn">
                        Daftar disini
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>