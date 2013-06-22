<?php
    include_once 'Unit.php';
    include_once 'Acuan.php';
    include_once 'Pemroses.php';

    session_start();
    $_SESSION["timestamp"] = "_" . date("H_i_s d.m.Y");       //timestamp untuk membedakan folder hasil.

    echo "<h2>HASIL MODUL #2</h2>";
    echo "<a href='./index.php'>Kembali Ke Halaman Utama</a><br/><br/>";
    $cobaPemroses   = new Pemroses(2);
    $cobaPemroses->pemrosesanUtamaModul_2();
?>
