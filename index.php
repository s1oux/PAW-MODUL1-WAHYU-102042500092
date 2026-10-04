<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset') {
    unset($_SESSION['products']);
    $_SESSION['message'] = "Data produk dan stok berhasil direset ke awal!";
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        [
            "id" => 1,
            "name" => "Zowie Monitor",
            "category" => "Monitor",
            "price" => 1800000,
            "stock" => 4,
            "image" => "gambar/zowie.jpeg",
            "rating" => 4.8,
            "reviews" => 104
        ],

        [ 
            "id" => 2,
            "name" => "Lenovo Ideapad Gaming 3",
            "category" => "Laptop",
            "price" => 15000000,
            "stock" => 3,
            "image" => "gambar/laptop.jpeg",
            "rating" => 4.9,
            "reviews" => 98 ],

        [ 
            "id" => 3,
            "name" => "Ajazz AK-820", 
            "category" => "Aksesoris", 
            "price" => 950000, 
            "stock" => 15, 
            "image" => "gambar/ajazz.jpeg", 
            "rating" => 4.7, 
            "reviews" => 86 
        ],

        [ 
            "id" => 4, 
            "name" => "FURYCUBE G9", 
            "category" => "Aksesoris", 
            "price" => 500000, 
            "stock" => 20, 
            "image" => "gambar/furycube.jpeg", 
            "rating" => 4.9, 
            "reviews" => 176
        ],

        [ 
            "id" => 5, 
            "name" => "IEM Kinera Celest Wyvern", 
            "category" => "Audio", 
            "price" => 1200000, 
            "stock" => 0, 
            "image" => "gambar/kinera.jpeg", 
            "rating" => 4.6, 
            "reviews" => 54
        ],

        [ 
            "id" => 6, 
            "name" => "OBSBOT Mini 2", 
            "category" => "Aksesoris", 
            "price" => 4500000, 
            "stock" => 8, 
            "image" => "gambar/obsbot.jpeg", 
            "rating" => 4.8, 
            "reviews" => 42
        ],
        
        [ 
            "id" => 7, 
            "name" => "Steam Deck OLED", 
            "category" => "Konsol", 
            "price" => 9500000, 
            "stock" => 5, 
            "image" => "gambar/steamdeck.jpeg", 
            "rating" => 4.9, 
            "reviews" => 210
        ],

        [ 
            "id" => 8, 
            "name" => "Sony PSP 3000", 
            "category" => "Konsol", 
            "price" => 1200000, 
            "stock" => 2, 
            "image" => "gambar/psp.jpeg", 
            "rating" => 4.7, 
            "reviews" => 845
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'beli') {
    $product_id = (int)$_POST['product_id'];
    $jumlah_beli = (int)$_POST['quantity'];

    foreach ($_SESSION['products'] as $key => $product) {
        if ($product['id'] === $product_id) {
            if ($product['stock'] >= $jumlah_beli && $jumlah_beli > 0) {
                $_SESSION['products'][$key]['stock'] -= $jumlah_beli;
                $_SESSION['message'] = "Berhasil membeli $jumlah_beli unit " . $product['name'] . "!";
            } else {
                $_SESSION['message'] = "Gagal! Stok tidak mencukupi.";
            }
            break;
        }
    }
    header("Location: index.php");
    exit;
}

$show_modal = false;
$modal_data = null;
if (isset($_GET['buy'])) {
    $buy_id = (int)$_GET['buy'];
    foreach ($_SESSION['products'] as $product) {
        if ($product['id'] === $buy_id && $product['stock'] > 0) {
            $show_modal = true;
            $modal_data = $product;
            break;
        }
    }
}

$products = $_SESSION['products'];
$total_products = count($products);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cia Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Cia Store</h1>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#">Products</a>
            <a href="#">About</a>
        </div>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert-success">
            <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <section class="hero-container">
        <div class="hero">
            <p class="subtitle">CIA STORE</p>
            <h2>Simple Tech Store.</h2>
            <p class="desc">Temukan berbagai perangkat dan aksesoris teknologi untuk kebutuhanmu.</p>
            <a href="#katalog" class="btn-light">Lihat Produk</a>
        </div>
    </section>

    <main class="katalog-section" id="katalog">
        <div class="katalog-header">
            <div class="katalog-title">
                <p>OUR PRODUCTS</p>
                <h3>Katalog Produk</h3>
            </div>
            
            <div class="katalog-actions">
                <form method="POST" action="index.php" style="margin: 0;">
                    <input type="hidden" name="action" value="reset">
                    <button type="submit" class="btn-reset">Reset Data & Stok</button>
                </form>
                <div class="total-badge">
                    Total Produk: <?= $total_products ?>
                </div>
            </div>
        </div>

        <div class="grid-produk">
            <?php 
            foreach ($products as $item): 
                $harga_asli = $item['price'];
                $stok = $item['stock'];
                
                $dapat_diskon = $harga_asli >= 1000000;
                $persentase_diskon = 10;
                $harga_akhir = $dapat_diskon ? $harga_asli - ($harga_asli * ($persentase_diskon / 100)) : $harga_asli;
                $status_tersedia = $stok > 0;
            ?>
                <div class="card">
                    <div class="card-image-container">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php if ($dapat_diskon): ?>
                            <span class="badge-diskon">-<?= $persentase_diskon ?>%</span>
                        <?php endif; ?>
                    </div>

                    <div class="card-content">
                        <h4 class="nama-produk"><?= htmlspecialchars($item['name']) ?></h4>
                        <p class="kategori"><?= htmlspecialchars($item['category']) ?></p>
                        
                        <div class="harga-container">
                            <span class="harga-akhir">Rp<?= number_format($harga_akhir, 0, ',', '.') ?></span>
                            <?php if ($dapat_diskon): ?>
                                <span class="harga-coret">Rp<?= number_format($harga_asli, 0, ',', '.') ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="rating-container">
                            <span class="star">&#9733;</span>
                            <span><?= htmlspecialchars($item['rating']) ?></span>
                            <span>(<?= htmlspecialchars($item['reviews']) ?>)</span>
                        </div>
                    </div>

                    <div class="card-stok-info">
                        <span style="font-size: 0.875rem; color: #64748b;">Stok: <?= $stok ?></span>
                        <?php if ($status_tersedia): ?>
                            <span class="status-badge tersedia">Tersedia</span>
                        <?php else: ?>
                            <span class="status-badge habis">Habis</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($status_tersedia): ?>
                        <a href="?buy=<?= $item['id'] ?>#katalog" class="btn-beli">Beli Sekarang</a>
                    <?php else: ?>
                        <a href="#" class="btn-beli disabled">Stok Habis</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php if ($show_modal && $modal_data): ?>
    <div class="modal-overlay">
        <div class="modal-content">
            <h4>Beli <?= htmlspecialchars($modal_data['name']) ?></h4>
            <p>Masukkan jumlah yang ingin dibeli.</p>
            
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="beli">
                <input type="hidden" name="product_id" value="<?= $modal_data['id'] ?>">
                
                <div class="form-group">
                    <label for="quantity">Jumlah (Maks: <?= $modal_data['stock'] ?>)</label>
                    <input type="number" name="quantity" min="1" max="<?= $modal_data['stock'] ?>" value="1" required>
                </div>
                
                <div class="modal-actions">
                    <a href="index.php" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-confirm">Konfirmasi Beli</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>



<!-- PAW-MODUL1-WAHYU-102042500092 -->
