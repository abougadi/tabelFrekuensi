<?php
    include_once 'Unit.php';
    include_once 'Acuan.php';
    include_once 'Pemroses.php';

    session_start();
    $_SESSION["timestamp"] = "_" . date("H_i_s d.m.Y");     //timestamp untuk membedakan folder hasil.

    $tambah_satu = true;
    if($_GET['tambah_satu'] == 'true')
    {
        $tambah_satu = true;
    }
    else
    {
        $tambah_satu = false;
    }

    $_SESSION['tambah_satu'] = $tambah_satu;                        // set jadi true, apabila pada pemrosesan rumus ingin menambahkan satu pada penyebut.

    echo "<h2>HASIL MODUL #5</h2>";
    echo "<a href='./index.php'>Kembali Ke Halaman Utama</a><br/><br/>";
    $cobaPemroses   = new Pemroses(5);
    $cobaPemroses->pemrosesanUtamaModul_5();
?>
