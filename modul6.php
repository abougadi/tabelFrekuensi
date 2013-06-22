<?php
    include_once 'Unit.php';
    include_once 'Acuan.php';
    include_once 'Pemroses.php';

    session_start();
    echo "<h2>HASIL MODUL #6</h2>";
    echo "<a href='./index.php'>Kembali Ke Halaman Utama</a><br/><br/>";
    $_SESSION["timestamp"] = "_" . date("H_i_s d.m.Y");     //timestamp untuk membedakan folder hasil.
    $_SESSION["gen_file_individu"] = false;
    if($_GET['gen_file_individu'] == 'true')
    {
        $_SESSION["gen_file_individu"] = true;
    }
    $cobaPemroses = new Pemroses(6);
?>
