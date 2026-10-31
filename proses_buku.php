
<?php

// Memanggil class Buku
require_once 'buku.php';

session_start();

// Menyiapkan session
if (!isset($_SESSION['buku'])) {
    $_SESSION['buku'] = [];
}

// Kategori yang tersedia
$kategoriPilihan = ["Fiksi", "Nonfiksi"];

// Memastikan form menggunakan POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Mengambil data dari form
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahun = $_POST['tahun'] ?? '';
    $kategori = $_POST['kategori'] ?? '';

    // Validasi judul dan penulis
    if ($judul == "" || $penulis == "") {

        $_SESSION['pesan'] = "Judul dan penulis wajib diisi.";

    }

    // Validasi tahun
    elseif (
        filter_var($tahun, FILTER_VALIDATE_INT) === false ||
        $tahun < 1000 ||
        $tahun > date("Y")
    ) {

        $_SESSION['pesan'] = "Tahun harus berupa angka yang valid.";

    }

    // Validasi kategori
    elseif (!in_array($kategori, $kategoriPilihan, true)) {

        $_SESSION['pesan'] = "Kategori harus Fiksi atau Nonfiksi.";

    }

    // Validasi file cover
    elseif (
        !isset($_FILES['cover']) ||
        $_FILES['cover']['error'] !== UPLOAD_ERR_OK
    ) {

        $_SESSION['pesan'] = "Cover buku wajib diupload.";

    }

    else {

        // Mengambil informasi file
        $namaFile = $_FILES['cover']['name'];
        $ukuranFile = $_FILES['cover']['size'];
        $tmpFile = $_FILES['cover']['tmp_name'];

        // Mengambil ekstensi file
        $ekstensi = strtolower(
            pathinfo($namaFile, PATHINFO_EXTENSION)
        );

        // Format file yang diperbolehkan
        $allowedTypes = [
            "jpg"  => "image/jpeg",
            "jpeg" => "image/jpeg",
            "png"  => "image/png",
            "webp" => "image/webp"
        ];

        // Memeriksa tipe file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpFile);
        finfo_close($finfo);

        // Validasi ukuran maksimal 2 MB
        if ($ukuranFile > 2 * 1024 * 1024) {

            $_SESSION['pesan'] = "Ukuran cover maksimal 2 MB.";

        }

        // Validasi ekstensi dan MIME
        elseif (
            !isset($allowedTypes[$ekstensi]) ||
            $mime !== $allowedTypes[$ekstensi]
        ) {

            $_SESSION['pesan'] = "Format cover harus JPG, JPEG, PNG, atau WEBP.";

        }

        else {

            // Membuat folder uploads jika belum ada
            if (!is_dir("uploads")) {
                mkdir("uploads", 0755, true);
            }

            // Membuat nama file unik
            $namaBaru = bin2hex(random_bytes(16)) . "." . $ekstensi;

            $lokasi = "uploads/" . $namaBaru;

            // Memindahkan file ke folder uploads
            if (move_uploaded_file($tmpFile, $lokasi)) {

                // Membuat objek Buku
                $bukuBaru = new Buku(
                    $judul,
                    $penulis,
                    $tahun,
                    $kategori,
                    $lokasi
                );

                // Menyimpan objek ke session
                $_SESSION['buku'][] = $bukuBaru;

                $_SESSION['pesan'] = "Buku berhasil ditambahkan!";

            } else {

                $_SESSION['pesan'] = "Gagal mengupload cover.";

            }

        }

    }

    // Kembali ke halaman utama
    header("Location: index.php");
    exit;

}

// Jika bukan POST, kembali ke halaman utama
header("Location: index.php");
exit;

?>
