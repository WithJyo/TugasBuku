
<?php
class Buku {
    public $judul;
    public $penulis;
    public $tahun;
    public $kategori;
    public $cover;
    // Constructor
    public function __construct($judul, $penulis, $tahun, $kategori, $cover) {

        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahun = $tahun;
        $this->kategori = $kategori;
        $this->cover = $cover;

    }
}
?>
