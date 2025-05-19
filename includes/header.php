<?php if (!isset($no_header)): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - Bank Sampah' : 'Bank Sampah' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 py-3 flex justify-between items-center">
            <a href="dashboard.php" class="text-xl sm:text-2xl font-bold text-blue-600 flex items-center hover:text-blue-700 transition-all duration-500 ease-in-out transform hover:scale-105">
                <i class="fas fa-recycle mr-2 sm:mr-3 text-2xl sm:text-3xl animate-spin-slow hover:animate-pulse"></i>
                <span class="bg-gradient-to-r from-blue-600 to-green-500 bg-clip-text text-transparent hover:from-green-500 hover:to-blue-600 transition-all duration-500">
                    Bank Sampah
                </span>
            </a>
            
            <!-- Mobile Menu Button -->
            <button id="mobileMenuButton" class="sm:hidden text-gray-600 hover:text-blue-600 focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>

            <div class="hidden sm:flex items-center space-x-4 sm:space-x-6">
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin_dashboard.php" class="text-gray-600 hover:text-blue-600 text-sm font-medium transition-all duration-300 ease-in-out transform hover:scale-105 hover:-translate-y-0.5">
                    <i class="fas fa-user-shield mr-1 sm:mr-2 transition-all duration-300 ease-in-out"></i> 
                    <span class="hidden sm:inline transition-all duration-300 ease-in-out">Admin Panel</span>
                </a>
                <?php endif; ?>
                <div class="relative group">
                    <img src="assets/uploads/<?= $_SESSION['profile_pic'] ?>" 
                         class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-blue-500 cursor-pointer hover:border-blue-600 transition-all duration-300"
                         id="profileDropdownBtn">
                    <div class="hidden group-hover:block absolute right-0 mt-2 w-48 sm:w-56 bg-white rounded-lg shadow-xl py-2 z-50 animate-fade-in" id="profileDropdown">
                        <a href="profile.php" class="flex items-center px-4 py-2 sm:py-3 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-300">
                            <i class="fas fa-user-circle mr-2 sm:mr-3 text-lg text-blue-500"></i>
                            <span>Profil Saya</span>
                        </a>
                        <a href="logout.php" class="flex items-center px-4 py-2 sm:py-3 text-sm text-gray-700 hover:bg-blue-50 transition-colors duration-300">
                            <i class="fas fa-sign-out-alt mr-2 sm:mr-3 text-lg text-red-500"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobileNav" class="hidden sm:hidden bg-white shadow-md">
            <div class="flex flex-col space-y-2 py-3">
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="admin_dashboard.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50 <?= basename($_SERVER['PHP_SELF']) === 'admin_dashboard.php' ? 'bg-blue-100 font-semibold text-blue-700 border-l-4 border-blue-500' : '' ?>">
                    <i class="fas fa-user-shield mr-2"></i> Admin Panel
                </a>
                <?php endif; ?>
                <a href="dashboard.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50 <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-blue-100 font-semibold text-blue-700 border-l-4 border-blue-500' : '' ?>">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="education.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50 <?= basename($_SERVER['PHP_SELF']) === 'education.php' ? 'bg-blue-100 font-semibold text-blue-700 border-l-4 border-blue-500' : '' ?>">
                    <i class="fas fa-seedling mr-2"></i> Edukasi
                </a>
                <a href="transactions.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50 <?= basename($_SERVER['PHP_SELF']) === 'transactions.php' ? 'bg-blue-100 font-semibold text-blue-700 border-l-4 border-blue-500' : '' ?>">
                    <i class="fas fa-exchange-alt mr-2"></i> Transaksi
                </a>
                <a href="report.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50 <?= basename($_SERVER['PHP_SELF']) === 'report.php' ? 'bg-blue-100 font-semibold text-blue-700 border-l-4 border-blue-500' : '' ?>">
                    <i class="fas fa-chart-line mr-2"></i> Laporan Bulanan
                </a>
                <a href="transfer.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50 <?= basename($_SERVER['PHP_SELF']) === 'transfer.php' ? 'bg-blue-100 font-semibold text-blue-700 border-l-4 border-blue-500' : '' ?>">
                    <i class="fas fa-exchange-alt mr-2"></i> Transfer Saldo
                </a>
                <a href="profile.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50 <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'bg-blue-100 font-semibold text-blue-700 border-l-4 border-blue-500' : '' ?>">
                    <i class="fas fa-user-circle mr-2"></i> Profil
                </a>
                <a href="logout.php" class="px-4 py-2 text-gray-600 hover:bg-blue-50">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>

        <!-- Desktop Navigation -->
        <nav class="bg-gradient-to-r from-blue-800 to-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4 sm:px-6">
                <div class="hidden sm:flex space-x-4 py-2 sm:py-0">
                    <a href="dashboard.php" 
                       class="px-3 py-2 sm:px-4 sm:py-3 hover:bg-blue-700/80 transition-colors duration-300 flex items-center space-x-2 <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-blue-700/90 font-semibold shadow-inner border-b-2 border-blue-300' : '' ?>">
                        <i class="fas fa-home text-lg"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="education.php"
                       class="px-3 py-2 sm:px-4 sm:py-3 hover:bg-blue-700/80 transition-colors duration-300 flex items-center space-x-2 <?= basename($_SERVER['PHP_SELF']) === 'education.php' ? 'bg-blue-700/90 font-semibold shadow-inner border-b-2 border-blue-300' : '' ?>">
                        <i class="fas fa-seedling text-lg"></i>
                        <span>Edukasi</span>
                    </a>
                    <a href="transactions.php"
                       class="px-3 py-2 sm:px-4 sm:py-3 hover:bg-blue-700/80 transition-colors duration-300 flex items-center space-x-2 <?= basename($_SERVER['PHP_SELF']) === 'transactions.php' ? 'bg-blue-700/90 font-semibold shadow-inner border-b-2 border-blue-300' : '' ?>">
                        <i class="fas fa-exchange-alt text-lg"></i>
                        <span>Transaksi</span>
                    </a>
                    <a href="report.php"
                       class="px-3 py-2 sm:px-4 sm:py-3 hover:bg-blue-700/80 transition-colors duration-300 flex items-center space-x-2 <?= basename($_SERVER['PHP_SELF']) === 'report.php' ? 'bg-blue-700/90 font-semibold shadow-inner border-b-2 border-blue-300' : '' ?>">
                        <i class="fas fa-chart-line text-lg"></i>
                        <span>Laporan Bulanan</span>
                    </a>
                    <a href="transfer.php"
                       class="px-3 py-2 sm:px-4 sm:py-3 hover:bg-blue-700/80 transition-colors duration-300 flex items-center space-x-2 <?= basename($_SERVER['PHP_SELF']) === 'transfer.php' ? 'bg-blue-700/90 font-semibold shadow-inner border-b-2 border-blue-300' : '' ?>">
                        <i class="fas fa-exchange-alt text-lg"></i>
                        <span>Transfer Saldo</span>
                    </a>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="container mx-auto px-4 sm:px-6 py-6 sm:py-8">
        <script>
            // Mobile menu toggle
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileNav = document.getElementById('mobileNav');
            
            mobileMenuButton.addEventListener('click', () => {
                mobileNav.classList.toggle('hidden');
            });
        </script>
<?php endif; ?>