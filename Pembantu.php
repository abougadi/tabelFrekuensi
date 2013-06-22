<?php

/**
 * Class ini berisi fungsi-fungsi pembantu untuk semua proses yang ada.
 *
 * @author Abu Bakar Gadi
 */
class Pembantu {
    /*****************/
    /* FUNGSI-FUNGSI */
    /*****************/

    /********************/
    /* Pembantu MODUL 1-2 */
    /********************/
    //public static function muatFileEDU($path,&$arr_jml_kata_per_edu,&$arr_jml_kata_per_sentence) :: mengatur nilai array jumlah kata per EDU dan per Sentence.
    public static function muatFileEDU($path,&$arr_jml_kata_per_edu,&$arr_jml_kata_per_sentence)
    {
        //Ambil jumlah kata untuk tiap unit
        $fptr = fopen($path, 'r');

        $buff = fgets($fptr);
        $arr_jml_kata_per_edu = array();        // menyimpan jumlah kata per unit EDU.
        $arr_jml_kata_per_sentence = array();   // menyimpan jumlah kata per unit sentence.

        $total_kata_tmp = 0;
        while($buff)
        {
            $buff = trim($buff);
            $tmpArr = explode(" ", $buff);
            if($tmpArr[count($tmpArr)-1] == "<S>")
            {
                unset( $tmpArr[count($tmpArr)-1] );     //menghilangkan tag <S> dari array.
                $tmpArr = array_values($tmpArr);
                $total_kata_tmp += count($tmpArr);
                array_push($arr_jml_kata_per_sentence,$total_kata_tmp);
                $total_kata_tmp = 0;
            }
            else if($tmpArr[count($tmpArr)-1] == "<E>")     //menghilangkan tag <E> dari array.
            {
                unset( $tmpArr[count($tmpArr)-1] );    //removes sentence and EDU indicator string from array.
                $tmpArr = array_values($tmpArr);
                $total_kata_tmp += count($tmpArr);
            }
            else
            {
                $total_kata_tmp += count($tmpArr);
            }

            array_push($arr_jml_kata_per_edu,count($tmpArr));
            $buff = fgets($fptr);
        }
        fclose($fptr);
    }

    //public static function muatFileNERPOS($path,&$arr_word,&$arr_token,&$arr_pos,&$arr_ner) :: memuat semua token dan nerpos dari file NERPOS.
    public static function muatFileNERPOS($path,&$arr_word,&$arr_token,&$arr_pos,&$arr_ner)
    {
        $fptr = fopen($path, 'r');

        $buff = fgets($fptr);
        $arr_word = array();        //menampung word
        $arr_token = array();       //menampung token (lemma)
        $arr_pos = array();         //menampung POS
        $arr_ner = array();         //menampung NER

        while($buff)
        {
            $tmpArr = explode("\t", $buff);
            array_push($arr_word, $tmpArr[2]);
            array_push($arr_token, $tmpArr[3]);
            array_push($arr_pos, $tmpArr[6]);
            array_push($arr_ner, trim($tmpArr[7]));
            $buff = fgets($fptr);
        }

        fclose($fptr);
    }

    /********************/
    /* Pembantu MODUL 3 */
    /********************/
    //public static function muatListToken() :: memuat file list token ke $objekUnitIUF
    public static function muatListTokenInputModul3(&$objekUnitIUF)
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_LT))
        {
            echo "Data tidak lengkap.. Persiapkan folder input tabel frekuensi untuk modul 3 : '".Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_LT."' <br/>";
            exit(0);
        }

        $path = "";
        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_LT);

        while(false !== ($file = readdir($handle)))
        {
            if(!strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_LIST_TOKEN)), Umum::AKHIRAN_LIST_TOKEN))
            {
                $path = Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_LT . $file;
                break;
            }
//            $file = readdir($handle);       // ambil file pertama. //aboubakrTmp 170113 : fix wrong file lookup method. do not read twice.
        }

//echo "Path List Token Mod3 : " . $path; //aboubakrTmp
        $fptr = fopen($path, 'r');

        $buff = fgets($fptr);

        while($buff)
        {
            $buff = str_replace("\n", "", $buff);
            array_push($objekUnitIUF['array_list_token'], $buff);
            $buff = fgets($fptr);
        }

        fclose($fptr);
    }

    //public static function muatTabelFrekuensiInputModul3($path,&$objekUnitIUF)    :: memuat file tabel frekuensi memasukkan ke array_frekuensi_unit dalam objek_unit_IUF (lihat inisialisasi untuk modul 3 di kelas Pemroses). Harus dipanggil setelah inisialisasi file list token.
    public static function muatTabelFrekuensiInputModul3($path,&$objekUnitIUF)
    {
//        $this->objek_unit_IUF   = array('tipe'=>'ED','array_list_token'=>array(),'array_frekuensi_unit'=>array()); //tipe -> edu, sentence, atau dokumen ; array_frekuensi_unit -> hasil pembacaan dari file frekuensi.
        $fptr = fopen($path, 'r');

        $buff = fgets($fptr);
        $arr_container = array();
        while($buff)
        {
            $tmpArr = explode(",", $buff);
            if(count($objekUnitIUF['array_list_token']) == count($tmpArr))        // sinkronkan dengan array_list_token, cukup cek panjang array saja. --> jumlah token harus sama dengan jumlah data frekuensi.
            {
                $tmpArr[count($tmpArr)-1] = str_replace("\n", "", $tmpArr[count($tmpArr)-1]);   // hilangkan \n dari elemen terakhir.
//    for($i=0;$i<count($tmpArr);$i++)
//    {
//        $tmpArr[$i] = intval($tmpArr[$i]);
//    }
                array_push($arr_container, $tmpArr);
            }
            $buff = fgets($fptr);
        }

        array_push($objekUnitIUF['array_frekuensi_unit'], $arr_container);

        fclose($fptr);
    }

    /********************/
    /* Pembantu MODUL 4 */
    /********************/
//public static function muatNilaiIUFModul4(&$arrayIUF)    :: memuat IUF dari file masukan ke $arrayIUF.
    public static function muatNilaiIUFModul4(&$arrayIUF)
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_IUF))
        {
            echo "Data tidak lengkap.. Persiapkan folder input nilai IUF untuk modul 4 : '".Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_IUF."' <br/>";
            exit(0);
        }

        $path = "";
        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_IUF);

        while(false !== ($file = readdir($handle)))       // ambil file pertama.
        {
            if(!strcasecmp($file, Umum::AKHIRAN_MASUKAN_MODUL_4_IUF))
            {
                $path = Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_IUF . $file;
                break;
            }
//            $file = readdir($handle);     //aboubakrTmp 170113 : fix wrong file lookup method. do not read twice.
        }
        if($path == "")
        {
            echo "File IUF Tidak tersedia dalam folder '".Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_IUF."', Program dihentikan...<br/>";
            exit(0);
        }

//echo "Path Nilai IUF Modul 4: " . $path . "<br/>";
        $fptr = fopen($path, 'r');
        $buff = fgets($fptr);
        $arrayIUF = array();
        while($buff)
        {
            $buff = str_replace("\n", "", $buff);   // hilangkan \n dari ujung string.
            array_push($arrayIUF, $buff);
            $buff = fgets($fptr);
        }
        fclose($fptr);
    }

    //public static function muatTabelFrekuensiInputModul4($path,&$objekUnitIUF)    :: memuat file tabel frekuensi memasukkan ke array_frekuensi_unit dalam objek_unit_IUF (lihat inisialisasi untuk modul 4 di kelas Pemroses). Harus dipanggil setelah inisialisasi file list token.
    public static function muatTabelFrekuensiInputModul4($path,&$objekUnitIUF)
    {
//        $this->objek_unit_IUF   = array('tipe'=>'ED','array_list_token'=>array(),'array_frekuensi_unit'=>array()); //tipe -> edu, sentence, atau dokumen ; array_frekuensi_unit -> hasil pembacaan dari file frekuensi.
        $fptr = fopen($path, 'r');

        $buff = fgets($fptr);
        $arr_container = array();
        while($buff)
        {
            $tmpArr = explode(",", $buff);
            $tmpArr[count($tmpArr)-1] = str_replace("\n", "", $tmpArr[count($tmpArr)-1]);   // hilangkan \n dari elemen terakhir.
            array_push($arr_container, $tmpArr);

            $buff = fgets($fptr);
        }
        array_push($objekUnitIUF['array_frekuensi_unit'], $arr_container);

        fclose($fptr);
    }

    /********************/
    /* Pembantu MODUL 5 */
    /********************/
    //public static function muatArrayTargetModul5($path,&$arrayTarget) :: tambahkan hasil muat file target ke index terakhir.
    public static function muatArrayTargetModul5($path,&$arrayTarget)
    {
        $totalIndex = count($arrayTarget);
        $fptr = fopen($path, 'r');
        $buff = fgets($fptr);

        $arrayTarget[$totalIndex] = array();
        while($buff)
        {
            $buff = str_replace("\n", "", $buff);   // hilangkan \n dari ujung string.
            $buff = str_replace("\r", "", $buff);   // hilangkan \r dari ujung string.
            $tmpArray = explode(',', $buff);

            array_push($arrayTarget[$totalIndex], $tmpArray);
            $buff = fgets($fptr);
        }
        fclose($fptr);
    }


    //public static function muatArrayAcuanModul5(&$arrayAcuan)
    public static function muatArrayAcuanModul5(&$arrayNamaFileAcuan,&$arrayAcuan,&$tipeUnit)
    {
        $path = "";
        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_5 . Umum::FOLDER_MASUKAN_MODUL_5_ACUAN);
        $arrayNamaFileAcuan = array();
        $tipeUnit = "";
        while(false !== ($file = readdir($handle)))       // ambil file pertama yang cocok dengan akhiran.
        {
            if(!strncasecmp(substr($file,-4), ".txt", 4))  // file .txt
            {
//echo "FILE : " . substr($file, -4) . "<br/>";
                if($tipeUnit == "")
                {
                    $tipeUnit = substr($file, -6, 2);
                }
                $path = Umum::FOLDER_MASUKAN_MODUL_5 . Umum::FOLDER_MASUKAN_MODUL_5_ACUAN . $file;
                array_push($arrayNamaFileAcuan, substr($file, 0, strlen($file)-4));
                break;
            }
//            $file = readdir($handle);//aboubakrTmp 170113 : fix wrong file lookup method. do not read twice.
        }
        if($path == "")
        {
            echo "File Acuan Tidak tersedia dalam folder '".Umum::FOLDER_MASUKAN_MODUL_5 . Umum::FOLDER_MASUKAN_MODUL_5_ACUAN."', Program dihentikan...<br/>";
            exit(0);
        }

        echo "File Acuan yang digunakan : " . $path . "<br/>";

        $fptr = fopen($path, 'r');
        $buff = fgets($fptr);
        $arrayAcuan = array();
        while($buff)
        {
            $buff = str_replace("\n", "", $buff);   // hilangkan \n dari ujung string.
            $tmpArray = explode(',', $buff);

            array_push($arrayAcuan, $tmpArray);
            $buff = fgets($fptr);
        }
        fclose($fptr);
    }

    /********************/
    /* Pembantu MODUL 6 */
    /********************/
    public static function muatNilaiSimilarityScore($namaFile)
    {
        $fptr = fopen(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_SIMSCR . $namaFile, "r");

        $hasil = fgets($fptr);
        $arraySimScore = explode(",", $hasil);
        fclose($fptr);
        return $arraySimScore;
    }

    /*****************/
    /* Pembantu Umum */
    /*****************/
    //public static function hitungKata($kata,$array,$index_mulai,$index_akhir) :: fungsi untuk menghitung jumlah kemunculan kata $kata dalam array $array, diantara dua index, index_mulai dan index_akhir.
    public static function hitungKata($kata,$arrayKata,$arrayNERPOS,$indexAwalNERPOS,$penandaFilterTokenEntity,$index_mulai,$index_akhir,$multipleEDU=false)
    {
        $jumlah_kata = 0;
        $idxAktif = 0;

        $arrayKataPisah = explode(" ", $kata);
        $idxAkhir = count($arrayKataPisah) - 1;

        $nerSebelumnya = -999;     //20120907 : hanya dipakai di penghitungan kata single.
        $nerSetelahnya = -999;     //20120907 : hanya dipakai di penghitungan kata single.

        for($i=$index_mulai;$i<$index_akhir;$i++)
        {
            if($idxAkhir > 0)   // menangani kata yang terdiri lebih dari satu bagian (dipisahkan spasi).
            {
                $targetValid = false;
                $idxNERPOS = $i + $indexAwalNERPOS;   //index NER untuk kata yang sedang aktif sekarang dalam unit.

                if($penandaFilterTokenEntity & Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]))
                {
                    $targetValid = true;        //20130612 : flag untuk validasi hasil cek target.
                    $NERKataPertama = Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]);
                    while(($i <= $index_akhir) && ($idxAktif <= $idxAkhir))               //cek index, jangan melebihi batas akhir.
                    {
                        if(!strcasecmp($arrayKataPisah[$idxAktif],$arrayKata[$i]))
                        {
                            $idxAktif++;        //index aktif, menunjukkan index dari gabungan kata target yang dicari. jika masih menemukan kata yang sama dalam unit, maka lanjutkan cek ke index berikutnya.
                            $i++;               //masih menemukan sub kata yang sama, cek ke index kata berikutnya dalam unit.
                        }
                        else
                        {
                            $targetValid = false;
                            break;              //exit from loop.
                        }
                    }
                    $idxNERPOS = $i + $indexAwalNERPOS;
                    if($targetValid && ($NERKataPertama == Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]))) // 20130612 : cukup cek kata berikutnya, kalau NER nya sama.. berarti tidak valid...
                    {
                        $targetValid = false;
                    }    
                }

                if($targetValid)
                {
                    $jumlah_kata++;
                }
                $idxAktif = 0;
            }
            else    // 20130401 Pengecekan untuk kata single.
            {
                $idxNERPOS = $i + $indexAwalNERPOS;
                //20120907 : ambil nilai NER sebelum dan setelah kata yang sedang dicek.
                if($i > 0)
                {
                    $nerSebelumnya = Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS-1]);
                    if(isset($arrayNERPOS[$idxNERPOS+1]))
                    {
                        $nerSetelahnya = Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS+1]);
                    }
                    else
                    {
                        $nerSetelahnya = -999;
                    }
                }
                else
                {
                    $nerSebelumnya = -999;
                    $nerSetelahnya = Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS+1]);
                }

                //aboubakr : 20120903 -> ubah kondisi untuk menghitung. yang kemarin kurang pas kondisinya, jadi ngga pernah masuk hituungan. T_T
                //if( ((($penandaFilterTokenEntity & Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS])) && $penandaFilterTokenEntity > Umum::FILTER_MASK_POS)   //  jika dipilih filter NER (nilai filter lebih dari Umum::FILTER_MASK_POS) .
                //        || ($penandaFilterTokenEntity <= Umum::FILTER_MASK_POS))                                                                        //  jika tidak dipilih filter NER.
                //      && !strcasecmp($arrayKataPisah[0],$arrayKata[$i]) ) //20130401 arrayKataPisah pasti berisi 1 elemen, dalam kondisi ini, biar ga bingung, langsung pakai $arrayKataPisah[0].
                
                //16062013 : Ubah kondisi, tidak perlu dibedakan lagi handling perhitungan antara NER dan POS.
                if(($penandaFilterTokenEntity & Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]))
                    && !strcasecmp($arrayKataPisah[0],$arrayKata[$i]) ) //20130401 arrayKataPisah pasti berisi 1 elemen, dalam kondisi ini, biar ga bingung, langsung pakai $arrayKataPisah[0].
                {
                    //20120907 : masih butuh pengecekan apakah kata yang akan dihitung benar2 sama. kalau kebetulan ditemukan substring dari kata lain dengan NER sama, tetep jangan dihitung...
                    // Cek tag NER dari kata sebelum atau sesudahnya. Tidak boleh ada yg sama. Intinya Kata yang dijadikan target pengecekan, tidak boleh terdiri lebih dari satu kata.
                    //20120910 : tambahkan kondisi, || (Pembantu::ambilNilaiTag($arrayNER[$idxNER]) == 0) -> untuk menangani pilihan kriteria lemma atau tanpa kriteria.
                    if( (Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]) != $nerSebelumnya && Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]) != $nerSetelahnya) || (Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]) == 0) || ($penandaFilterTokenEntity <= Umum::FILTER_MASK_POS))
                    {
if($arrayKataPisah[0] == 'wear')
{
    echo "Chosen Filter: " . $penandaFilterTokenEntity . " ---> " . Pembantu::ambilNilaiTag($arrayNERPOS[$idxNERPOS]) . "<br/>";
}
                        $jumlah_kata++;
                    }
                }
            }
        }
//echo "Kata $kata :: $jumlah_kata<br/>";
        return $jumlah_kata;
    }

    //public static function hitungKata2($kata,$array,$index_mulai,$index_akhir) :: fungsi untuk menghitung jumlah kemunculan kata $kata dalam array $array, diantara dua index, index_mulai dan index_akhir.
    public static function hitungKata2($kata,$arrayKata,$index_mulai,$index_akhir)
    {
        $jumlah_kata = 0;
        $idxAktif = 0;
        $arrayKataPisah = explode(" ", $kata);
        $idxAkhir = count($arrayKataPisah) - 1;
        for($i=$index_mulai;$i<$index_akhir;$i++)
        {
            if($idxAkhir > 0)   // menangani kata yang terdiri lebih dari satu bagian (dipisahkan spasi).
            {
//echo "multiple words... $kata <br/>";
                while(($i < $index_akhir/*$idxAkhir*/) && ($idxAkhir >= $idxAktif) && !strcasecmp($arrayKataPisah[$idxAktif],$arrayKata[$i]))
                {
//echo "($idxAktif / $idxAkhir) --> " . $arrayKataPisah[$idxAktif] . " -- " . $array[$i] . "<br/>";
                    $idxAktif++;
                    $i++;
                }
//echo "($idxAktif / $idxAkhir)<br/>";
                //if($idxAktif >= $idxAkhir)  // gabungan kata cocok
                if($idxAktif > $idxAkhir)  // gabungan kata cocok
                {
                    $jumlah_kata++;
                    $idxAktif = 0;
                }
            }
            else
            {
                if(!strcasecmp($arrayKataPisah[$idxAktif],$arrayKata[$i]))
                {
                    $jumlah_kata++;
                }
            }
        }
        return $jumlah_kata;
    }

    //public static function cekToken($token,$array_stopwords,$array_idx_alphabet_stopwords) :: mengecek token, apakah perlu dimasukkan atau tidak.
    public static function cekToken($token,$array_stopwords,$array_idx_alphabet_stopwords)
    {
        $tambahkan_token = true;            //secara default akan memasukkan token.
        if( ((($token[0] >= 'a')&&($token[0] <= 'z')) || (($token[0] >= 'A')&&($token[0] <= 'Z'))) )   // lewati special characters.
        {
            $CC = $token[0];    // ambil karakter pertama dari token. cari di list stopwords yang berawalan karakter sama.
            if(isset($array_idx_alphabet_stopwords[$CC]))
            {
                $j = $array_idx_alphabet_stopwords[$CC];
                $panjang_array_stopwords = count($array_stopwords);

                while(($j < $panjang_array_stopwords) && ($array_stopwords[$j][0] == $CC))     // memastikan dalam rentang stopwords yang berawalan sama. dan index masih di dalam rentang array stopwords.
                {
                    if(!strcasecmp($array_stopwords[$j],$token))    // menemukan kata stopwords.
                    {
                        $tambahkan_token = false;
                        break;
                    }
                    $j++;
                }
            }
        }
        else
        {
            $tambahkan_token = false;
        }

        return $tambahkan_token;
    }

    //public static function masukkanElemenTerurut(&$array,$elemen,$array_stopwords,$array_idx_alphabet_stopwords,$lewatiStopWords=false) :: memasukkan elemen ke array secara terurut.
    public static function masukkanElemenUnikTerurut(&$array,$elemen,$array_stopwords,$array_idx_alphabet_stopwords,$lewatiStopWords=false)
    {
        $idx = -1;          // mulai dari -1, karena akan diincrement dulu sebelum diproses.
        $panjang_array = count($array);
        $perlu_ditambahkan = true;
        $hasil_perbandingan = 0;

        //filtering stopwords.
        if(!self::cekToken($elemen,$array_stopwords,$array_idx_alphabet_stopwords) && !$lewatiStopWords)
        {
            return false;
        }
//echo "[abouDebug]masukkan token $elemen <br/>";
        while($idx < $panjang_array)
        {
            $idx++;
            if($panjang_array == 0 || $idx == $panjang_array)
            {
                $perlu_ditambahkan = true;
                break;
            }

            $hasil_perbandingan = strcasecmp($array[$idx], $elemen);

            if($hasil_perbandingan == 0)  //menemukan elemen sama, skip.
            {
                $perlu_ditambahkan = false;
                break;
            }
            else if($hasil_perbandingan > 0)
            {
                $perlu_ditambahkan = true;
                break;
            }
            else
            {
                ;
            }
        }
//echo "[abouDebug]tambahkan elemen " . $elemen . " <br/>";
        if($perlu_ditambahkan){
            array_splice($array,$idx,0,$elemen);
            return $idx;        // kembalikan tempat index elemen disimpan.
        }
    }

//    20130401 : tidak perlu, gunakan fungsi bawaan dari php untuk membuat array unique.
//    public static function cekArrayUnik($elemen,&$arrayTarget)
//    {
//        $count = count($arrayTarget);
//        for($i=0;$i<$count;$i++)
//        {
//            $hasil_perbandingan = strcasecmp($arrayTarget[$i], $elemen);
//            if(!$hasil_perbandingan)
//            {
//                Pembantu::cetakDebug("RE-UNIQUE $elemen in " . json_encode($arrayTarget) . "<br/>");
//            }
//        }
//    }

    public static function hitungBarisFile($alamatFile)
    {
        $jumlahBaris = 0;
        $str = "";
        $fptr = fopen($alamatFile, 'r');
        while($str = fgets($fptr))
        {
            if($str != "")
            {
                $jumlahBaris++;
            }
        }
        fclose($fptr);

        return $jumlahBaris;
    }

    public static function ambilNilaiTag($tag)
    {
        switch(true)
        {
// POS
            case strstr($tag, "NN") :
                return Umum::FILTER_POS_NN;
            case strstr($tag, "VB") :
                return Umum::FILTER_POS_VB;
            case strstr($tag, "JJ") :
                return Umum::FILTER_POS_JJ;
            case strstr($tag, "PRP") :
                return Umum::FILTER_POS_PRP;
// NER
            case strstr($tag, "PERSON") :
                return Umum::FILTER_NER_PERSON;
            case strstr($tag, "LOCATION") :
                return Umum::FILTER_NER_LOCATION;
            case strstr($tag, "ORGANIZATION") :
                return Umum::FILTER_NER_ORGANIZATION;
            case strstr($tag, "DATE") :
                return Umum::FILTER_NER_DATE;
            case strstr($tag, "MONEY") :
                return Umum::FILTER_NER_MONEY;
            case strstr($tag, "TIME") :
                return Umum::FILTER_NER_TIME;
            case strstr($tag, "NUMBER") :
                return Umum::FILTER_NER_NUMBER;
            case strstr($tag, "ORDINAL") :
                return Umum::FILTER_NER_ORDINAL;
            case strstr($tag, "MISC") :
                return Umum::FILTER_NER_MISC;
            default:
//echo "unfound POS : $tag <br/>";
                //return Umum::FILTER_TANPA_KRITERIA;       //aboubakr 20120903 -> ganti nilai return, agar dianggap selalu benar.
                //return 0xFFFF;
                return 0x00;        //aboubakr 20120906 -> ganti nilai return, agar dianggap selalu salah. -_-" default harusnya salah.. biar ngga ngaco hasilnyaa...
        }
    }

    public static function gunakanLemma($pilihanKriteria)
    {
        if( ($pilihanKriteria & Umum::FILTER_LEMMA) || (($pilihanKriteria <= Umum::FILTER_MASK_POS) && ($pilihanKriteria > Umum::FILTER_LEMMA)) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function cetakDebug($str)
    {
//echo $_SESSION["debug"] . "<br/>";
        if($_SESSION["debug"])
        {
            echo $str;
        }
        else
        {
            //TODO 20120907 : cetak ke dump file, bila perlu.
        }
    }

}
?>
