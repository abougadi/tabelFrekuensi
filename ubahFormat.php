<?php
    include 'Umum.php';

    session_start();
    $_SESSION["timestamp"] = "_" . date("H_i_s d.m.Y");       //timestamp untuk membedakan folder hasil.

    $folder = $_GET["folder"] . Umum::AKHIRAN_HASIL_UBAH_FORMAT . $_SESSION["timestamp"];

    echo "<h2>MODUL Ubah Format Keluaran</h2>";

    /* Pemrosesan Hasil */

    if(!is_dir($_GET["folder"]))
    {
        echo "Folder '" . $_GET["folder"] . "' tidak ditemukan... Masukkan nama folder yang valid. (relatif terhadap index.php)<br/>";
        echo "<a href='./index.php'>Kembali Ke Halaman Utama</a><br/><br/>";
        exit(0);
    }
    echo "Folder hasil pengubahan format ada di '$folder'.<br/><br/>";
    echo "<a href='./index.php'>Kembali Ke Halaman Utama</a><br/><br/>";

    $handle = opendir($_GET["folder"]);
    $array_nama_file_target = array();

    if($handle)
    {
        $i = 0;
        while(false !== ($file = readdir($handle)))
        {
            if($file != "." && $file != "..")
            {
                array_push($array_nama_file_target, $file);    // yang disimpan hanya bagian nama utamanya saja, hilangkan
                $i++;
            }
        }
        closedir($handle);
    }

//    echo "<br/>" . json_encode($array_nama_file_target);

    if(!is_dir($folder))
    {
        mkdir($folder,0777,true);  // buat folder keluaran.
    }

    $totalMasukan = count($array_nama_file_target);
    for($i=0; $i < $totalMasukan; $i++)
    {
        //Baca file masukan, simpan dalam array.
        $arrayHasil = array();
        $fptr = fopen($_GET["folder"] . "/" . $array_nama_file_target[$i],"r");
        while($buff = fgets($fptr))
        {
            $buff = trim($buff);
            array_push($arrayHasil, explode(",", $buff));
        }

        fclose($fptr);

        //Tulis Hasil Transpose.
        $string_hasil = "";

        $total_index1 = count($arrayHasil[0]);
        $total_index2 = count($arrayHasil);
        for($j=0;$j<$total_index1;$j++)
        {
            for($k=0;$k<$total_index2;$k++)
            {
                $string_hasil .= $arrayHasil[$k][$j];
                if($k < $total_index2-1) $string_hasil .= ",";       //tambahkan tanda koma diantara nilai frekuensi
            }
            if($j < $total_index1 - 1) $string_hasil .= "\n";       //tambahkan line break diantara nilai frekuensi untuk tiap unit
        }

        $fptr = fopen($folder . "/" . $array_nama_file_target[$i],"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }



?>
