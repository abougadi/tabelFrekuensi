<?php
    include_once 'Unit.php';
    include_once 'Acuan.php';
    include_once 'Pemroses.php';

    session_start();
    $_SESSION["timestamp"] = "_" . date("H_i_s d.m.Y");     //timestamp untuk membedakan folder hasil.
    $_SESSION["per_folder"] = true;

    $tambah_satu = true;
    $per_folder = true;

    if($_GET['tambah_satu'] == 'true')
    {
        $tambah_satu = true;
    }
    else
    {
        $tambah_satu = false;
    }

//    if($_GET['per_folder'] == 'true')
//    {
//        $per_folder = true;
//    }
//    else
//    {
//        $per_folder = false;
//    }

    $_SESSION['tambah_satu'] = $tambah_satu;                        // set jadi true, apabila pada pemrosesan rumus ingin menambahkan satu pada penyebut.

    echo "<h2>HASIL MODUL #3</h2>";
    echo "<a href='./index.php'>Kembali Ke Halaman Utama</a><br/><br/>";
    $cobaPemroses   = new Pemroses(3);  //aboubakr 27012013 : set $_SESSION["per_folder"] here, depends on the document type.
if($_SESSION["per_folder"])
    echo "Process per folder<br/>";
else
    echo "DoNot process per folder<br/>";
    $cobaPemroses->pemrosesanUtamaModul_3($_SESSION["per_folder"]/*$per_folder*/);            // ganti parameter ke true jika ingin menggunakan modul 3 dengan pemrosesan per folder.
?>
