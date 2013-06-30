<?php
/**
 * Berisi fungsi-fungsi statis untuk penulisan hasil pemrosesan ke file keluaran.
 *
 * @author Abu Bakar Gadi
 */
class TulisHasil {

    //public static function tuliskanListToken_modul1($objek_acuan) :: Menuliskan list token file acuan ke file keluaran, untuk modul 1.
    public static function tuliskanListToken_modul1($objek_acuan)
    {
        $string_hasil = "";
        for($i=0;$i<count($objek_acuan->array_token_bersih);$i++)
        {
            $string_hasil .= $objek_acuan->array_token_bersih[$i];
            $string_hasil .= "\n";
        }

        $fptr = fopen(Umum::FOLDER_HASIL_1 . $_SESSION["timestamp"] . Umum::FOLDER_LIST_TOKEN . $objek_acuan->nama . Umum::AKHIRAN_LIST_TOKEN,"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }

    //public function tuliskanTabelFrekuensi($namaFile,$arrayHasil) :: menuliskan hasil pemrosesan tabel frekuensi, bisa digunakan untuk modul 1 dan 2.
    public static function tuliskanTabelFrekuensi($namaFile,$arrayHasil)
    {
echo "[abouDebug]TulisFile " . $namaFile . "<br/>";
        $string_hasil = "";
        $total_index1 = count($arrayHasil);
        for($i=0;$i<count($arrayHasil);$i++)
        {
            $total_index2 = count($arrayHasil[$i]);
            for($j=0;$j<$total_index2;$j++)
            {
                $string_hasil .= $arrayHasil[$i][$j];
                if($j < $total_index2-1) $string_hasil .= ",";       //tambahkan tanda koma diantara nilai frekuensi
            }
            if($i < $total_index1 - 1) $string_hasil .= "\n";       //tambahkan line break diantara nilai frekuensi untuk tiap unit
        }
        $fptr = fopen($namaFile,"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }

    //public static function tuliskanListToken_modul2($objek_acuan) :: Menuliskan list token file unit ke file keluaran, untuk modul 2.
    public static function tuliskanListToken_modul2($array_semua_token)
    {
        $string_hasil = "";
        for($i=0;$i<count($array_semua_token);$i++)
        {
            $string_hasil .= $array_semua_token[$i];
            $string_hasil .= "\n";
        }

        $fptr = fopen(Umum::FOLDER_HASIL_2 . $_SESSION["timestamp"] . Umum::FOLDER_LIST_TOKEN . Umum::NAMAFILE_LIST_TOKEN_MOD2,"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }

    //public static function tuliskanRingkasanFileUnit() :: menuliskan ringkasan jumlah token dari seluruh file di folder.
    public static function tuliskanRingkasanFileUnit($array_objek_file_unit,$array_nama_file)
    {
//echo "JUMLAH-1 " . count($array_nama_file) . json_encode($array_nama_file) . "<br/>";
//echo "JUMLAH-2 " . count($array_objek_file_unit) . json_encode($array_objek_file_unit) . "<br/>";
        if(!is_dir(Umum::FOLDER_HASIL_RINGKASAN . $_SESSION["timestamp"]))
        {
            mkdir(Umum::FOLDER_HASIL_RINGKASAN . $_SESSION["timestamp"]);
        }

        $fptr1 = fopen(Umum::FOLDER_HASIL_RINGKASAN . $_SESSION["timestamp"] . "/RingkasanToken" . Umum::AKHIRAN_FILE_RINGKASAN_ALL,'w');
        $fptr3 = fopen(Umum::FOLDER_HASIL_RINGKASAN . $_SESSION["timestamp"] . "/RingkasanUnit" . Umum::AKHIRAN_FILE_RINGKASAN_ALL,'w');
        $totalUnitEDU       = 0;
        $totalUnitSentence  = 0;

        for($i=0;$i<count($array_nama_file);$i++)
        {
            $filename = Umum::FOLDER_HASIL_RINGKASAN . $_SESSION["timestamp"] . "/" . $array_nama_file[$i] . Umum::AKHIRAN_FILE_RINGKASAN_TOK;
            $fptr2 = fopen($filename,'w');
            $string_hasil = "";

            $string_hasil .= "===========JUMLAH TOKEN PER EDU=============\n";
            $panjang1 = count($array_objek_file_unit[$i]->array_jml_kata_per_edu);
            $total_token = 0;
            $totalUnitEDU += $panjang1;

            for($j=0;$j<$panjang1;$j++)
            {
                $total_token += $array_objek_file_unit[$i]->array_jml_kata_per_edu[$j];
                $string_hasil .= "EDU ke-" . ($j+1) . " : " . $array_objek_file_unit[$i]->array_jml_kata_per_edu[$j] . "\n";
            }
            $string_hasil .= "******* TOTAL TOKEN EDU = ".$total_token." *******\n";

            $string_hasil .= "\n===========JUMLAH TOKEN PER SENTENCE=============\n";
            $panjang2 = count($array_objek_file_unit[$i]->array_jml_kata_per_sentence);
            $total_token = 0;
            $totalUnitSentence += $panjang2;

            for($j=0;$j<$panjang2;$j++)
            {
                $total_token += $array_objek_file_unit[$i]->array_jml_kata_per_sentence[$j];
                $string_hasil .= "SENTENCE ke-" . ($j+1) . " : " . $array_objek_file_unit[$i]->array_jml_kata_per_sentence[$j] . "\n";
            }
            $string_hasil .= "******* TOTAL TOKEN SENTENCE = ".$total_token." *******\n";

            fwrite($fptr2, $string_hasil);
            fclose($fptr2);
            fwrite($fptr1,"******* TOTAL TOKEN File '".$array_nama_file[$i]."' = ".$total_token." *******\n");
            fwrite($fptr3,"'".$array_nama_file[$i]."' = ".$panjang1." unit EDU -- ".$panjang2." unit Sentence\n");
        }
        fwrite($fptr3,"\n******* TOTAL EDU = ".$totalUnitEDU." *******\n");
        fwrite($fptr3,"******* TOTAL Sentence = ".$totalUnitSentence." *******\n");
        fclose($fptr1);
        fclose($fptr3);
    }

    //public static function tuliskanLemmaTF_modul3($namaFile,$listToken,$arrayHasil)
    public static function tuliskanLemma_TF_IUF_modul3($namaFile,$listToken,$arrayHasil)
    {
echo "<br/>[abouDebug]TulisFile " . $namaFile . "<br/>";
        $string_hasil = "";
        $total_index = count($arrayHasil);
        for($i=0;$i<$total_index;$i++)
        {
            $string_hasil .= $listToken[$i] . "," . $arrayHasil[$i];
            if($i < $total_index - 1) $string_hasil .= "\n";       //tambahkan line break diantara nilai iuf untuk tiap token.
        }
        $fptr = fopen($namaFile,"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }

    //public static function tuliskanIUF_modul3($namaFile,$arrayIUF) :: Menuliskan array IUF
    public static function tuliskanIUF_modul3($namaFile,$arrayIUF)
    {
        $string_hasil = "";
        $total_index = count($arrayIUF);
        for($i=0; $i < $total_index; $i++)
        {
            $string_hasil .= $arrayIUF[$i];
            if($i < $total_index-1)
            {
                $string_hasil .= "\n";
            }
        }

        $fptr = fopen($namaFile,"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }

    //public static function tuliskanHasilModul3($namaFile,$arrayIUF,$objekUnitIUF,$arrayNamaFileMasukan) :: Menuliskan hasil perhitungan IUF ==> TFIUF
    public static function tuliskanHasil_modul3($namaFolder,$arrayIUF,$objekUnitIUF,$arrayNamaFileMasukan)
    {
echo "<br/>[abouDebug]TulisFile Hasil Modul 3 :: " . $namaFolder . "<br/>";

        $arrayFrekuensiUnit = $objekUnitIUF['array_frekuensi_unit'];
        $string_hasil = "";
        $total_index0 = count($arrayFrekuensiUnit);
        $total_index2 = count($arrayIUF);
        $total_unit = 0;
        $string_hasil_semua = "";
        for($h=0;$h<$total_index0;$h++)         // array of array frekuensi unit untuk tiap file.
        {
            $total_index1 = count($arrayFrekuensiUnit[$h]);
            $total_unit += $total_index1;
            $string_hasil = "";
            for($i=0;$i<$total_index1;$i++)     // untuk tiap unitnya.
            {
                for($j=0;$j<$total_index2;$j++) // array IUF
                {
                    $nilai = $arrayIUF[$j] * $arrayFrekuensiUnit[$h][$i][$j];       // yang ditulis di hasil adalah perkalian antara Frekuensi unit dan IUF.
                    $nilai = round($nilai, 4);
                    $string_hasil .= $nilai;
                    if($j < $total_index2 - 1) $string_hasil .= ",";            //tambahkan line break untuk tiap unit.
                }
                $string_hasil .= "\n";
                
//Tulis hasil per-file
                $namaFileKeluaran = "";

                $panjangNama = strlen($arrayNamaFileMasukan[$h]) - strlen(Umum::AKHIRAN_TABEL_FREQ_EDU);    // buang akhiran nama file.
                $namaFileKeluaran = substr($arrayNamaFileMasukan[$h], 0, $panjangNama);
                $namaFileKeluaran .= "-TFIEF-" . $objekUnitIUF['tipe'] . ".txt";    //aboubakr 27012013 : update namafile keluaran.
//                if($objekUnitIUF['tipe'] == 'edu')
//                {
//                    $namaFileKeluaran .= Umum::AKHIRAN_HASIL_MOD3_EDU;
//                }
//                else if($objekUnitIUF['tipe'] == 'stc')
//                {
//                    $namaFileKeluaran .= Umum::AKHIRAN_HASIL_MOD3_STC;
//                }
//                else
//                {
//                    $namaFileKeluaran .= Umum::AKHIRAN_HASIL_MOD3_DOC;
//                }

                $fptr = fopen($namaFolder . $namaFileKeluaran,"w");
                fwrite($fptr, $string_hasil);
                fclose($fptr);
            }
            $string_hasil_semua .= $string_hasil;
        }
echo "Pemrosesan selesai... Total Unit = $total_unit<br/>";
        if($objekUnitIUF['tipe'] == 'edu' || $objekUnitIUF['tipe'] == 'ED')
        {
            $namaFileKeluaran = Umum::NAMAFILE_HASIL_MOD3_SEMUA_EDU;
        }
        else if($objekUnitIUF['tipe'] == 'sentence' || $objekUnitIUF['tipe'] == 'SD')
        {
            $namaFileKeluaran = Umum::NAMAFILE_HASIL_MOD3_SEMUA_STC;
        }
        else
        {
            $namaFileKeluaran = Umum::NAMAFILE_HASIL_MOD3_SEMUA_DOC;
        }
        $fptr = fopen($namaFolder . $namaFileKeluaran,"w");
        fwrite($fptr, $string_hasil_semua);
        fclose($fptr);
    }

    //public static function tuliskanHasilModul4($namaFolder,$arrayIUF,$objekUnitIUF,$arrayNamaFileMasukan) :: Menuliskan hasil perhitungan IUF ==> TFIUF
    public static function tuliskanHasil_modul4($namaFolder,$arrayIUF,$objekUnitIUF,$arrayNamaFileMasukan)
    {
echo "<br/>[abouDebug]TulisFile Hasil Modul 4 :: " . $namaFolder . "<br/>";
        $arrayFrekuensiUnit = $objekUnitIUF['array_frekuensi_unit'];
        $array_hasil = array();
        $total_index0 = count($arrayFrekuensiUnit);
        $total_index2 = count($arrayIUF);
        $total_unit = 0;

        for($h=0;$h<$total_index0;$h++)         // array of array frekuensi unit untuk tiap file.
        {
            $total_index1 = count($arrayFrekuensiUnit[$h]);
//echo "Total index 1 = ". $total_index1 . "<br/>";
            $total_unit += $total_index1;
            $array_hasil[$h] = array();         // baris baru untuk array hasil, unit baru.
            for($i=0;$i<$total_index1;$i++)     // untuk tiap unitnya.
            {
                $array_hasil[$h][$i] = array();
                for($j=0;$j<$total_index2;$j++) // array IUF
                {
                    $nilai = $arrayIUF[$j] * $arrayFrekuensiUnit[$h][$i][$j];   // yang ditulis di hasil adalah perkalian antara Frekuensi unit dan IUF.
                    $nilai = round($nilai, 4);
                    $array_hasil[$h][$i][$j] = $nilai;                          //masukkan ke array hasil.
                }
            }
        }

        $total_index        = count($array_hasil);
        $string_hasil       = "";
        $string_hasil_semua = "";
        for($i = 0; $i < $total_index; $i++)        // untuk tiap file.
        {
            $total_index0 = count($array_hasil[$i]);
            $string_hasil = "";
            for($j = 0; $j < $total_index0; $j++)        // untuk tiap unit dalam file.
            {
                // Penghitungan rumus normal weight.
                $parameter2 = 0;
                $total_index1 = count($array_hasil[$i][$j]);
                for($k = 0; $k < $total_index1; $k++)       // hitung parameter 2 (pembagi pada formula normal weight).
                {
                    $parameter2 += ($array_hasil[$i][$j][$k] * $array_hasil[$i][$j][$k]);
                }
                $parameter2 = sqrt($parameter2);
                $parameter2 = round($parameter2, 4);

                for($k = 0; $k < $total_index1; $k++)               // hitung dan simpan hasil di array_hasil.
                {
    //echo $array_hasil[$i][$j][$k] . " / " . $parameter2 . "<br/>";
                    if($_SESSION['tambah_satu'])
                    {
                        $hasil =  $array_hasil[$i][$j][$k] / ($parameter2 + 1);
                    }
                    else
                    {
                        $hasil =  $array_hasil[$i][$j][$k] / $parameter2;
                    }
                    $array_hasil[$i][$j][$k] = round($hasil, 4);
                    $string_hasil .= "" . $array_hasil[$i][$j][$k];
                    if($k < $total_index1-1) $string_hasil .= ",";  // tambahkan tanda koma, sebagai pemisah antar nilai normal weight.
                }
                $string_hasil .= "\n";     // tambahkan new line, sebagai pemisah antar unit normal weight.
            }

            //Tulis hasil per-file
            $namaFileKeluaran = "";
            $panjangNama = strlen($arrayNamaFileMasukan[$i]) - strlen(Umum::AKHIRAN_TABEL_FREQ_EDU);
            $namaFileKeluaran = substr($arrayNamaFileMasukan[$i], 0, $panjangNama);

            if($objekUnitIUF['tipe'] == 'edu' || $objekUnitIUF['tipe'] == 'ED')
            {
                $namaFileKeluaran .= Umum::AKHIRAN_HASIL_MOD4_EDU;
            }
            else if($objekUnitIUF['tipe'] == 'sentence' || $objekUnitIUF['tipe'] == 'SD')
            {
                $namaFileKeluaran .= Umum::AKHIRAN_HASIL_MOD4_STC;
            }
            else
            {
                $namaFileKeluaran .= Umum::AKHIRAN_HASIL_MOD4_DOC;
            }
            $fptr = fopen($namaFolder . $namaFileKeluaran,"w");
            fwrite($fptr, $string_hasil);
            fclose($fptr);
            $string_hasil_semua .= $string_hasil;
            //$string_hasil .= "---------------------NEW_FILE : '" . $arrayNamaFileMasukan[$i] . "' ---------------------\n";
        }
echo "Pemrosesan selesai... Total Unit = $total_unit<br/>";
        if($objekUnitIUF['tipe'] == 'edu' || $objekUnitIUF['tipe'] == 'ED')
        {
            $namaFileKeluaran = Umum::NAMAFILE_HASIL_MOD4_SEMUA_EDU;
        }
        else if($objekUnitIUF['tipe'] == 'sentence' || $objekUnitIUF['tipe'] == 'SD')
        {
            $namaFileKeluaran = Umum::NAMAFILE_HASIL_MOD4_SEMUA_STC;
        }
        else
        {
            $namaFileKeluaran = Umum::NAMAFILE_HASIL_MOD4_SEMUA_DOC;
        }
        $fptr = fopen($namaFolder . $namaFileKeluaran,"w");
        fwrite($fptr, $string_hasil_semua);
        fclose($fptr);
    }

    public static function tuliskanHasil_modul5($namaFileAcuan,$arrayNamaFileTarget,$arrayHasilSimilarityScore,$tipeUnit)
    {
        switch($tipeUnit)
        {
            case "ED" :
                $akhiranNamaFile = Umum::AKHIRAN_HASIL_MOD5_EDU;
                break;
            case "SD" :
                $akhiranNamaFile = Umum::AKHIRAN_HASIL_MOD5_STC;
                break;
            case "SD" :
            default:
                $akhiranNamaFile = Umum::AKHIRAN_HASIL_MOD5_DOC;
                break;
        }

        $totalHasil = count($arrayHasilSimilarityScore);
        for($i=0; $i<$totalHasil;$i++)
        {
            //$alamatFileKeluaran = Umum::FOLDER_HASIL_5 . $_SESSION["timestamp"] . "/" . $urutanHasil . "_" . $namaFileAcuan . "___" . $arrayNamaFileTarget[$i] . $akhiranNamaFile;
//echo "File keluaran - $i : " . $alamatFileKeluaran . "<br/>";
            //$fptr = fopen($alamatFileKeluaran,"w");
            $totalElemen = count($arrayHasilSimilarityScore[$i]);
            for($j=0;$j < $totalElemen; $j++)
            {
                $alamatFileKeluaran = Umum::FOLDER_HASIL_5 . $_SESSION["timestamp"] . "/#" . ($i+1) . "_" . ($j+1) . "_" . $namaFileAcuan . "___" . $arrayNamaFileTarget[$i] . $akhiranNamaFile;
echo "File keluaran - $i : " . $alamatFileKeluaran . "<br/>";
                $fptr = fopen($alamatFileKeluaran,"w");
                $result = implode(",", $arrayHasilSimilarityScore[$i][$j]);
                fwrite($fptr, $result);
                fclose($fptr);
            }
            //fclose($fptr);
        }
    }

    //public static function tuliskanHasilSeleksiUnit($namaFile,$arrayHasilSeleksi) :: Menuliskan hasil seleksi.
    public static function tuliskanHasilSeleksiUnit($namaFile,$arrayHasilSeleksi)
    {
        $string_hasil = "";
        $total_index = count($arrayHasilSeleksi);
        for($i=0; $i < $total_index; $i++)
        {
            $string_hasil .= $arrayHasilSeleksi[$i];
            if($i < $total_index-1)
            {
                $string_hasil .= "\n";
            }
        }

        $fptr = fopen($namaFile,"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }

    //public function tuliskanTabelFrekuensiSeleksiUnit($namaFile,$arrayHasil) :: menuliskan hasil pemrosesan tabel frekuensi pada modul selection unit.
    public static function tuliskanTabelFrekuensiSeleksiUnit($namaFile,$arrayHasil,$transpose)
    {
echo "[abouDebug]tuliskanTabelFrekuensiSeleksiUnit " . $namaFile . "<br/>";
        $string_hasil = "";

        if($transpose)
        {
            $total_index1 = count($arrayHasil[0]);
            $total_index2 = count($arrayHasil);
            for($i=0;$i<$total_index1;$i++)
            {
                for($j=0;$j<$total_index2;$j++)
                {
                    $string_hasil .= $arrayHasil[$j][$i];
                    if($j < $total_index2-1) $string_hasil .= ",";       //tambahkan tanda koma diantara nilai frekuensi
                }
                if($i < $total_index1 - 1) $string_hasil .= "\n";       //tambahkan line break diantara nilai frekuensi untuk tiap unit
            }
        }
        else
        {
            $total_index1 = count($arrayHasil);
            for($i=0;$i<count($arrayHasil);$i++)
            {
                $total_index2 = count($arrayHasil[$i]);
                for($j=0;$j<$total_index2;$j++)
                {
                    $string_hasil .= $arrayHasil[$i][$j];
                    if($j < $total_index2-1) $string_hasil .= ",";       //tambahkan tanda koma diantara nilai frekuensi
                }
                if($i < $total_index1 - 1) $string_hasil .= "\n";       //tambahkan line break diantara nilai frekuensi untuk tiap unit
            }
        }

        
        $fptr = fopen($namaFile,"w");
        fwrite($fptr, $string_hasil);
        fclose($fptr);
    }

    public static function tuliskanHasil_modul6($arraySimScoreTerurut,$namaFileHasil)
    {
        if(!is_dir(Umum::FOLDER_HASIL_6 . $_SESSION["timestamp"]))
        {
            mkdir(Umum::FOLDER_HASIL_6 . $_SESSION["timestamp"]);
        }

        // Tulis semuanya disini...
        $totalElemen = count($arraySimScoreTerurut['simScore']);
        //$stringHasil = '(Score)&nbsp;&nbsp;&nbsp;[documentIndex]~[UnitIndex]##Unit String Value<br/>';
        $stringHasil = "(Score)\t[documentIndex]~[UnitIndex]##Unit String Value\n";
        //$stringHasil .= '*******************(Whole File)*************************<br/>';
        $stringHasil .= "*******************(Whole File)*************************\n";
        for($i = 0; $i < $totalElemen; $i++)
        {
            $stringHasil .= number_format($arraySimScoreTerurut['simScore'][$i], 3) . "\t";                     // Similarity Score
            //$stringHasil .= $arraySimScoreTerurut['simScore'][$i] . '&nbsp;&nbsp;&nbsp;';                     // Similarity Score
            $stringHasil .= $arraySimScoreTerurut['docIdx'][$i] . '~' . $arraySimScoreTerurut['unitIdx'][$i];   // [documentIndex]~[UnitIndex]
            $stringHasil .= '##'. $arraySimScoreTerurut['unitStr'][$i] . "\n";                                  // Unit String value
            //$stringHasil .= '##'. $arraySimScoreTerurut['unitStr'][$i] . '<br/>';                             // Unit String value
        }
echo "<span style='color:blue'><b>File Keluaran SimilarityScore Terurut : '" . Umum::FOLDER_HASIL_6 . $_SESSION['timestamp'] . "/" . $namaFileHasil  . "'</b></span><br/>";
        $fptr = fopen(Umum::FOLDER_HASIL_6 . $_SESSION["timestamp"] . "/" . $namaFileHasil, 'w');
        fwrite($fptr, $stringHasil);
        fclose($fptr);
    }
}
?>
