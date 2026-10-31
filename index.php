
<?php

// Memanggil class sebelum session dimulai
require_once 'buku.php';

session_start();

// Fungsi untuk mengamankan output
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Kategori buku
$kategoriPilihan = ["Fiksi", "Nonfiksi"];

// Mengambil pesan dari session
$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);

// Mengambil kategori dari GET
$filterKategori = $_GET['kategori'] ?? '';

// Validasi kategori pencarian
if (
    $filterKategori !== '' &&
    !in_array($filterKategori, $kategoriPilihan, true)
) {
    $filterKategori = '';
}

// Menyiapkan hasil pencarian
$hasilBuku = [];

foreach ($_SESSION['buku'] ?? [] as $buku) {

    // Menampilkan semua buku atau sesuai kategori
    if (
        $filterKategori == '' ||
        $buku->kategori == $filterKategori
    ) {

        $hasilBuku[] = $buku;

    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Data Buku</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            padding: 30px;
        }

        .container {
            max-width: 750px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        button {
            background: rgb(65, 112, 214);
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .buku {
            display: flex;
            gap: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #ddd;
        }

        .buku img {
            width: 100px;
            height: 130px;
            object-fit: cover;
        }

        .pesan {
            color: green;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Manajemen Data Buku</h1>

    <!-- PESAN -->
    <?php if ($pesan != ''): ?>

        <p class="pesan">
            <?= escape($pesan) ?>
        </p>

    <?php endif; ?>


    <!-- FORM POST -->
    <div class="card">

        <h2>Tambah Buku</h2>

        <form action="proses_buku.php"
              method="POST"
              enctype="multipart/form-data">

            <label>Judul Buku</label>

            <input type="text"
                   name="judul"
                   required>


            <label>Penulis</label>

            <input type="text"
                   name="penulis"
                   required>


            <label>Tahun Terbit</label>

            <input type="number"
                   name="tahun"
                   min="1000"
                   max="<?= date('Y') ?>"
                   required>


            <label>Kategori</label>

            <select name="kategori" required>

                <option value="">-- Pilih Kategori --</option>

                <?php foreach ($kategoriPilihan as $kategori): ?>

                    <option value="<?= escape($kategori) ?>">
                        <?= escape($kategori) ?>
                    </option>

                <?php endforeach; ?>

            </select>


            <label>Cover Buku</label>

            <input type="file"
                   name="cover"
                   accept=".jpg,.jpeg,.png,.webp"
                   required>


            <button type="submit">
                Tambah Buku
            </button>

        </form>

    </div>


    <!-- FORM GET -->
    <div class="card">

        <h2>Cari Buku Berdasarkan Kategori</h2>

        <form action="index.php" method="GET">

            <label>Pilih Kategori</label>

            <select name="kategori">

                <option value="">Semua Kategori</option>

                <?php foreach ($kategoriPilihan as $kategori): ?>

                    <option
                        value="<?= escape($kategori) ?>"
                        <?= $filterKategori == $kategori ? 'selected' : '' ?>
                    >
                        <?= escape($kategori) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <button type="submit">
                Cari Buku
            </button>

        </form>

    </div>


    <!-- DAFTAR BUKU -->
    <div class="card">

        <h2>Daftar Buku</h2>

        <?php if (count($hasilBuku) > 0): ?>

            <?php foreach ($hasilBuku as $buku): ?>

                <div class="buku">

                    <img
                        src="<?= escape($buku->cover) ?>"
                        alt="Cover buku"
                    >

                    <div>

                        <h3>
                            <?= escape($buku->judul) ?>
                        </h3>

                        <p>
                            Penulis: <?= escape($buku->penulis) ?>
                        </p>

                        <p>
                            Tahun: <?= escape($buku->tahun) ?>
                        </p>

                        <p>
                            Kategori: <?= escape($buku->kategori) ?>
                        </p>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>Belum ada buku atau data tidak ditemukan.</p>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
