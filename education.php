<?php
require_once 'includes/auth_check.php';
require_once 'functions.php';

// Data video edukasi
$videos = [
    [
        'id' => 'C2AFd1PLsF8', 
        'title' => 'Hindia - Berdansalah, Karir Tak Ada Artinya',
        'channel' => 'Hindia',
        'thumbnail' => 'https://img.youtube.com/vi/C2AFd1PLsF8/maxresdefault.jpg'
    ],
    [
        'id' => 'lB8ASupNtlw',
        'title' => 'Hindia - everything u are',
        'channel' => 'Hindia',
        'thumbnail' => 'https://img.youtube.com/vi/lB8ASupNtlw/maxresdefault.jpg'
    ],
    [
        'id' => 'iQo-8wx0l0Y',
        'title' => '.Feast - Nina (Official Lyric Video)',
        'channel' => '.Feast',
        'thumbnail' => 'https://img.youtube.com/vi/iQo-8wx0l0Y/maxresdefault.jpg'
    ],
    [
        'id' => 'w8pjAV8u2OE',
        'title' => '.Feast - Tarot (Official Lyric Video)',
        'channel' => '.Feast',
        'thumbnail' => 'https://img.youtube.com/vi/w8pjAV8u2OE/maxresdefault.jpg'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Edukasi Pengelolaan Sampah - Bank Sampah</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .article-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 12px;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .category-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .hero-section {
            background-image: url('assets/edu/hero-bg.jpg');
            background-size: cover;
            background-position: center;
        }
        @media (max-width: 640px) {
            .hero-section {
                padding: 1.5rem;
            }
            .hero-section h1 {
                font-size: 1.75rem;
            }
            .hero-section p {
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <main class="container mx-auto px-4 sm:px-6 py-8">
        <!-- Hero Section -->
        <div class="hero-section bg-gradient-to-r from-green-600 to-blue-700 rounded-2xl p-6 sm:p-8 md:p-12 text-white mb-8 sm:mb-12">
            <div class="max-w-3xl backdrop-blur-sm bg-black/30 p-4 sm:p-6 rounded-xl">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3 sm:mb-4">Edukasi Pengelolaan Sampah</h1>
                <p class="text-base sm:text-lg mb-4 sm:mb-6">Pelajari cara mengelola sampah dengan bijak untuk lingkungan yang lebih bersih dan sehat</p>
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <span class="bg-white bg-opacity-20 px-3 sm:px-4 py-1 rounded-full text-xs sm:text-sm">#ZeroWaste</span>
                    <span class="bg-white bg-opacity-20 px-3 sm:px-4 py-1 rounded-full text-xs sm:text-sm">#DaurUlang</span>
                    <span class="bg-white bg-opacity-20 px-3 sm:px-4 py-1 rounded-full text-xs sm:text-sm">#EcoFriendly</span>
                </div>
            </div>
        </div>        

        <!-- Video Edukasi -->
        <section class="mb-8 sm:mb-12">
            <div class="flex justify-between items-center mb-4 sm:mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Video Tutorial</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6">
                <?php foreach($videos as $video): ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="video-container">
                        <iframe src="https://www.youtube.com/embed/<?= $video['id'] ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <div class="p-3 sm:p-4">
                        <h3 class="font-bold text-sm sm:text-base mb-1"><?= $video['title'] ?></h3>
                        <p class="text-xs sm:text-sm text-gray-600"><?= $video['channel'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Tips Cepat -->
        <section class="bg-green-50 rounded-2xl p-4 sm:p-6 md:p-8 mb-8 sm:mb-12">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Tips Cepat Pengelolaan Sampah</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-3 sm:p-4 rounded-lg flex items-start">
                    <div class="bg-green-100 p-2 rounded-full mr-3 text-green-600">
                        <i class="fas fa-trash-alt text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-sm sm:text-base mb-1">Pemilahan</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Pisahkan sampah organik, anorganik, dan B3</p>
                    </div>
                </div>
                
                <div class="bg-white p-3 sm:p-4 rounded-lg flex items-start">
                    <div class="bg-blue-100 p-2 rounded-full mr-3 text-blue-600">
                        <i class="fas fa-recycle text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-sm sm:text-base mb-1">3R</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Reduce, Reuse, Recycle</p>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg flex items-start">
                    <div class="bg-yellow-100 p-2 rounded-full mr-3 text-yellow-600">
                        <i class="fas fa-compost"></i>
                    </div>
                    <div>
                        <h3 class="font-medium mb-1">Kompos</h3>
                        <p class="text-sm text-gray-600">Olah sampah organik jadi pupuk</p>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg flex items-start">
                    <div class="bg-purple-100 p-2 rounded-full mr-3 text-purple-600">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div>
                        <h3 class="font-medium mb-1">Belanja Bijak</h3>
                        <p class="text-sm text-gray-600">Bawa tas belanja sendiri</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="bg-white rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Pertanyaan Umum</h2>
            
            <div class="space-y-4">
                <div class="border-b border-gray-200 pb-4">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="font-medium">Bagaimana cara memulai memilah sampah?</h3>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content mt-2 text-gray-600 hidden">
                        <p>Mulailah dengan menyediakan 3 tempat sampah terpisah: organik, anorganik, dan B3. Beri label jelas dan edukasi seluruh anggota keluarga.</p>
                    </div>
                </div>
                
                <div class="border-b border-gray-200 pb-4">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="font-medium">Apa saja yang termasuk sampah B3?</h3>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content mt-2 text-gray-600 hidden">
                        <p>Sampah B3 (Bahan Berbahaya dan Beracun) contohnya: baterai, lampu neon, obat kadaluarsa, elektronik rusak, dan bahan kimia rumah tangga.</p>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-4">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="font-medium">Bagaimana cara membuat kompos dari sampah organik?</h3>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content mt-2 text-gray-600 hidden">
                        <p>Kumpulkan sampah organik seperti sisa makanan dan daun, lalu masukkan ke dalam komposter. Tambahkan bahan kering seperti serbuk gergaji, aduk secara berkala, dan jaga kelembapan. Kompos akan siap dalam 2-3 bulan.</p>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-4">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="font-medium">Apa manfaat daur ulang sampah plastik?</h3>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content mt-2 text-gray-600 hidden">
                        <p>Daur ulang plastik mengurangi polusi, menghemat energi, mengurangi penggunaan bahan baku baru, dan menciptakan lapangan kerja di industri daur ulang.</p>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-4">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="font-medium">Bagaimana cara mengurangi sampah di rumah?</h3>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content mt-2 text-gray-600 hidden">
                        <p>Gunakan produk yang bisa diisi ulang, bawa tas belanja sendiri, kurangi penggunaan plastik sekali pakai, dan beli produk dengan kemasan minimal.</p>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-4">
                    <button class="faq-toggle flex justify-between items-center w-full text-left">
                        <h3 class="font-medium">Apa itu bank sampah dan bagaimana cara bergabung?</h3>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content mt-2 text-gray-600 hidden">
                        <p>Bank sampah adalah sistem pengelolaan sampah berbasis masyarakat. Untuk bergabung, cari bank sampah terdekat, daftarkan diri, dan ikuti petunjuk pemilahan serta penyerahan sampah yang berlaku.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        // FAQ Toggle
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('i');
                
                content.classList.toggle('hidden');
                icon.classList.toggle('transform');
                icon.classList.toggle('rotate-180');
            });
        });
    </script>
</body>
</html>