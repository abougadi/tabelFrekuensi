<?php
    include_once 'Unit.php';
    include_once 'Acuan.php';
    include_once 'SeleksiUnit.php';

    session_start();
    $_SESSION["timestamp"] = "_" . date("H_i_s d.m.Y");       //timestamp untuk membedakan folder hasil.

    if($_GET['debug'] == 'true')
    {
        $_SESSION["debug"] = true;
    }
    else
    {
        $_SESSION["debug"] = false;
    }

    /* Ubah parameter pertama dari constructor SeleksiUnit untuk mengubah kriteria filtering.
       Lihat list detail konstanta pada file Umum.php. cari pada bagian "Konstanta SeleksiUnit" => "penanda filter token dan NER".
       contoh penggunaan : set parameter kedua dengan nilai Umum::FILTER_POS_NN untuk mendapatkan hasil filter NN. */

    /* Ubah parameter kedua dari constructor SeleksiUnit untuk mengubah unit string.
       Lihat list detail konstanta pada file Umum.php. cari pada bagian "Konstanta SeleksiUnit" => "penanda unit string".
       contoh penggunaan : set parameter ketiga dengan nilai Umum::UNIT_STRING_SINGLE_EDU untuk memilih unit string berdasarkan single EDU. */
    //$cobaSeleksiUnit = new SeleksiUnit(Umum::FILTER_POS_VB,Umum::UNIT_STRING_DOUBLE_EDU);

    echo "<h2>HASIL MODUL SELEKSI UNIT</h2>";
    echo "<a href='./index.php'>Kembali Ke Halaman Utama</a><br/><br/>";

    $mask = 0;
    for($i=0; $i < count($_GET['kriteria']); $i++)
    {
        $mask |= $_GET['kriteria'][$i];
    }
//echo dechex($mask) . "<br/>";
//echo decbin($mask);
//die();
    $cobaSeleksiUnit = new SeleksiUnit($mask,$_GET['unit_string']);
    $cobaSeleksiUnit->pemrosesanSeleksiUnit();
?>
