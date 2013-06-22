<?php
include_once 'Unit.php';
include_once 'Acuan.php';
include_once 'TulisHasil.php';

/**
 * Didalam class ini terdapat fungsi2 untuk memproses perhitungan term frequency yang berhubungan dengan unit dan file acuan.
 *
 * @author Abu Bakar Gadi
 */
class Pemroses {

    /*************/
    /* ATTRIBUTE */
    /*************/
    // sebelum pemrosesan modul
    private $array_nama_file_unit;          // array yang menyimpan list nama entitas file news dalam satu folder input.
    private $array_nama_file_acuan;         // array yang menyimpan list nama entitas file acuan dalam satu folder input.
    private $objek_file_acuan;              // objek file acuan.
    private $array_stopwords;               // array stopwords, diambil dari file stopwords.
    private $array_index_alph_stopwords;    // array index dari alphabet. menggunakan array associative. mulai 'a' sampai 'z' jika terdapat pada daftar kata stopwords.

    private $array_nama_file_input_mod3;    // array nama file untuk input tabel frekuensi modul 3.

    private $array_nama_file_input_mod4;    // array nama file untuk input tabel frekuensi modul 4.
    private $objek_unit_IUF;                // objek unit IUF. terdiri dari : 1.) array frekuensi token dalam tiap unit. 2.) list token.
    private $array_IUF_input_mod4;          // array untuk input modul 4, nilai IUF.

    private $array_nama_file_acuan_mod5;    // array nama file untuk input Acuan tabel frekuensi modul 5.
    private $array_nama_file_target_mod5;   // array nama file untuk input Target tabel frekuensi modul 5.
    private $array_nilai_acuan_mod5;        // hasil memuat file2 acuan modul 5.
    private $array_nilai_target_mod5;       // hasil memuat file2 target modul 5.
    private $tipeUnit_modul5_6;             // tipe unit file untuk modul 5 dan 6. ED, SD, atau DD.

    private $array_nama_file_unit_mod6;     // array nama file untuk input modul 6.
    private $array_nama_file_sim_scr_mod6;  // array nama file similarity score untuk input modul 6.
    private $array_objek_unit_mod6;         // array objek untuk input modul 6.
    private $array_nilai_similarity_score;  // array nilai similarity score untuk modul 6.
    private $jumlahBarisFileAcuan;          // menunjukkan jumlah baris file acuan yang dipakai untuk menghasilkan similarityScore.

    // setelah pemrosesan modul
    private $array_objek_file_unit;         // array yang menyimpan list objek entitas file news dalam satu folder file unit.
    private $array_token_seluruh_file;
    private $array_hasil_IUF;               // array yang menyimpan hasil pemrosesan formula IUF.
    private $array_hasil_similarity_score;  // array yang menyimpan hasil pemrosesan similarityScore pada modul 5.
    private $array_hasil_sim_score_urut;    // array yang menyimpan hasil pengurutan similarityScore pada modul 6.
    private $array_hasil_sim_score_per_file;// array yang menyimpan hasil pengurutan similarityScore pada modul 6, disimpan perfile.

    /*****************/
    /* FUNGSI-FUNGSI */
    /*****************/
    public function __construct($modul) {
        if($modul == 1 || $modul == 2)      // untuk modul 1 / 2.
        {
            $this->array_nama_file_unit         = array();
            $this->array_objek_file_unit        = array();
            $this->array_token_seluruh_file     = array();
            $this->array_stopwords              = array();
            $this->array_index_alph_stopwords   = array();

            //Inisialisasi File Unit dan File Acuan.
            $this->persiapkanDaftarStopwords();
            $this->muatNamaFileUnit();
            $this->muatNamaFileAcuan();
            $this->muatObjekFileUnit();
            TulisHasil::tuliskanRingkasanFileUnit($this->array_objek_file_unit, $this->array_nama_file_unit);    //Tuliskan ringkasan jumlah token.
        }
        else if($modul == 3)
        {
            $this->objek_unit_IUF   = array('tipe'=>'ED','array_list_token'=>array(),'array_frekuensi_unit'=>array(),'array_total'=>array()); //tipe -> edu, sentence, atau dokumen ; array_frekuensi_unit -> hasil pembacaan dari file frekuensi; array_total -> total frekuensi tiap token untuk semua file.
            $this->array_hasil_IUF  = array();
            $this->array_nama_file_input_mod3 = array();
            $this->persiapkanModul3();
//echo json_encode($this->objek_unit_IUF);
        }
        else if($modul == 4)
        {
            $this->objek_unit_IUF   = array('tipe'=>'ED','array_list_token'=>array(),'array_frekuensi_unit'=>array(),'array_total'=>array()); //pada modul 4, yang dipakai hanya array_frekuensi_unit.
            $this->array_nama_file_input_mod4 = array();
            $this->array_IUF_input_mod4 = array();
            $this->persiapkanModul4();
//echo json_encode($this->objek_unit_IUF);
//exit(0);
        }
        else if($modul == 5)
        {
            $this->array_nama_file_acuan_mod5 = array();
            $this->array_nama_file_target_mod5 = array();
            $this->array_nilai_acuan_mod5 = array();
            $this->array_nilai_target_mod5 = array();
            $this->array_hasil_similarity_score = array();
            $this->tipeUnit_modul5_6 = "";
            $this->persiapkanModul5();
//echo "Acuan : " . json_encode($this->array_nilai_acuan_mod5) . "<br/>";
//echo "Target : " . json_encode($this->array_nilai_target_mod5);
//exit(0);
        }
        else if($modul == 6)
        {
            $this->array_nama_file_unit_mod6 = array();
            $this->array_objek_unit_mod6 = array();
            $this->array_hasil_sim_score_urut = array();
            $this->array_hasil_sim_score_per_file = array();    //aboubakrTmp 020213 : tambahkan penyimpanan nilai similarity score, terurut untuk tiap file.
            $this->array_nama_file_sim_scr_mod6 = array();
            $this->array_nilai_similarity_score = array();
            $this->pemrosesanUtamaModul_6($_SESSION["gen_file_individu"]);
        }
    }

    private function persiapkanDaftarStopwords()
    {
        $CC = '-';
        $idx = 0;
        $fptr = fopen(Umum::ALAMAT_FILE_STOPWORDS, "r");
        $buff = "";

        while($buff = fgets($fptr))
        {
            array_push($this->array_stopwords,strtolower(trim($buff)));
            if($buff[0] != $CC) // mendeteksi perubahan huruf pertama. simpan indexnya.
            {
                $this->array_index_alph_stopwords["".$buff[0]] = $idx;  // catat index untuk tiap awal kata dengan alphabet berbeda. untuk efisiensi searching kata.
            }
            $CC = $buff[0];     // catat karakter pertama dari kata yang sedang dibaca.
            $idx++;
        }
        fclose($fptr);
    }

    /***********/
    /* MODUL 1 */
    /***********/
    //public function pemrosesanUtamaModul_1() :: pemrosesan utama untuk modul 1.
    public function pemrosesanUtamaModul_1()
    {
        //Buat folder-folder output
        mkdir(Umum::FOLDER_HASIL_1 . $_SESSION["timestamp"] . Umum::FOLDER_LIST_TOKEN,0777,true);
        mkdir(Umum::FOLDER_HASIL_1 . $_SESSION["timestamp"] . Umum::FOLDER_TABEL_FREQ_EDU,0777,true);
        mkdir(Umum::FOLDER_HASIL_1 . $_SESSION["timestamp"] . Umum::FOLDER_TABEL_FREQ_STC,0777,true);

        //lakukan pemrosesan utama
        for($i=0;$i<count($this->array_nama_file_acuan);$i++)               // untuk tiap file Acuan, lakukan pemrosesan untuk semua file unit
        {
            $this->ubahFileAcuan($this->array_nama_file_acuan[$i]);         // ubah file acuan, kemudian lanjutkan proses.
            TulisHasil::tuliskanListToken_modul1($this->objek_file_acuan);  // Tulis list Token ke file keluaran.

            for($j=0;$j<count($this->array_objek_file_unit);$j++)           // proses untuk tiap file unit, terhadap fileAcuan.
            {
                $hasil_edu = $this->pemrosesanTabelFrekuensi_edu($this->objek_file_acuan,$this->array_objek_file_unit[$j]);
                $hasil_sentence = $this->pemrosesanTabelFrekuensi_sentence($this->objek_file_acuan,$this->array_objek_file_unit[$j]);

                TulisHasil::tuliskanTabelFrekuensi(Umum::FOLDER_HASIL_1 . $_SESSION["timestamp"] .  Umum::FOLDER_TABEL_FREQ_EDU . $this->objek_file_acuan->nama . Umum::PENGHUBUNG_NAMAFILE . $this->array_objek_file_unit[$j]->nama . Umum::AKHIRAN_TABEL_FREQ_EDU, $hasil_edu);
                TulisHasil::tuliskanTabelFrekuensi(Umum::FOLDER_HASIL_1 . $_SESSION["timestamp"] .  Umum::FOLDER_TABEL_FREQ_STC . $this->objek_file_acuan->nama . Umum::PENGHUBUNG_NAMAFILE . $this->array_objek_file_unit[$j]->nama . Umum::AKHIRAN_TABEL_FREQ_STC, $hasil_sentence);
            }
        }
    }

    private function ubahFileAcuan($fileAcuanBaru)
    {
        $this->objek_file_acuan = new Acuan($fileAcuanBaru,$this->array_stopwords, $this->array_index_alph_stopwords);
    }

    //private function muatNamaFileUnit() :: ambil semua nama file unit di folder FileUnit.
    private function muatNamaFileUnit()
    {
        if(!is_dir(Umum::FOLDER_UNIT))
        {
            echo "Data tidak lengkap.. Persiapkan file2 hasil konversi XML... untuk file model dan file news...<br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_UNIT);
        $this->array_nama_file_unit = array();

        if($handle)
        {
            $i = 0;
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != ".." && !strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_NERPOS_FILE)), Umum::AKHIRAN_NERPOS_FILE))
                {
                    array_push($this->array_nama_file_unit, substr($file, 0, 0-strlen(Umum::AKHIRAN_NERPOS_FILE)) );    // yang disimpan hanya bagian nama utamanya saja, hilangkan
                    $i++;
                }
            }
            closedir($handle);
        }
    }

    private function muatNamaFileAcuan()
    {
        if(!is_dir(Umum::FOLDER_ACUAN))
        {
            echo "Data tidak lengkap.. Persiapkan file2 hasil konversi XML... untuk file model dan file news...<br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_ACUAN);
        $this->array_nama_file_acuan = array();

        if($handle)
        {
            $i = 0;
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != ".." && !strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_NERPOS_FILE)), Umum::AKHIRAN_NERPOS_FILE))
                {
                    array_push($this->array_nama_file_acuan, substr($file, 0, 0-strlen(Umum::AKHIRAN_NERPOS_FILE)) );    // yang disimpan hanya bagian nama utamanya saja, hilangkan
                    $i++;
                }
            }
            closedir($handle);
        }
    }

    //private function muatObjekFileUnit() :: buat semua objek unit dari semua file yang ada di FileUnit.
    private function muatObjekFileUnit()
    {
        for($i=0; $i < count($this->array_nama_file_unit); $i++)
        {
            $objek = new Unit($this->array_nama_file_unit[$i],Umum::FOLDER_UNIT);
            array_push($this->array_objek_file_unit, $objek);
        }
    }

    //private function pemrosesanTabelFrekuensi_edu($objek_acuan,$objek_unit) :: pemrosesan tabel frekuensi untuk satu unit file berdasarkan edu.
    private function pemrosesanTabelFrekuensi_edu($objek_acuan,$objek_unit)
    {
        $array_hasil = array();
        $awal = 0;
        $akhir = 0;
        $index = 0;
        while($index < count($objek_unit->array_jml_kata_per_edu))
        {
            $akhir = $awal + $objek_unit->array_jml_kata_per_edu[$index];

            $hasil_tmp = array();
            for($i = 0; $i < count($objek_acuan->array_token_bersih);$i++)
            {
                $jumlah_kata = Pembantu::hitungKata2($objek_acuan->array_token_bersih[$i],$objek_unit->array_token,$awal,$akhir);
                array_push($hasil_tmp, $jumlah_kata);
            }
            array_push($array_hasil, $hasil_tmp);
            $awal = $akhir;
            $index++;
        }
//echo "[abouDebug]HasilPemrosesan " . $objek_unit->nama . " : " . json_encode($array_hasil) . "<br/><br/>";
        return $array_hasil;
    }

    //private function pemrosesanTabelFrekuensi_sentence($objek_acuan,$objek_unit) :: pemrosesan tabel frekuensi untuk satu unit file berdasarkan sentence.
    private function pemrosesanTabelFrekuensi_sentence($objek_acuan,$objek_unit)
    {
        $array_hasil = array();
        $awal = 0;
        $akhir = 0;
        $index = 0;
        while($index < count($objek_unit->array_jml_kata_per_sentence))
        {
            $akhir = $awal + $objek_unit->array_jml_kata_per_sentence[$index];

            $hasil_tmp = array();
            for($i = 0; $i < count($objek_acuan->array_token_bersih);$i++)
            {
                $jumlah_kata = Pembantu::hitungKata2($objek_acuan->array_token_bersih[$i],$objek_unit->array_token,$awal,$akhir);
                array_push($hasil_tmp, $jumlah_kata);
            }
            array_push($array_hasil, $hasil_tmp);
            $awal = $akhir;
            $index++;
        }
//echo "[abouDebug]HasilPemrosesan Sentence " . $objek_unit->nama . " : " . json_encode($array_hasil) . "<br/>";
        return $array_hasil;
    }

    /***********/
    /* MODUL 2 */
    /***********/
    //public function pemrosesanUtamaModul_2() :: pemrosesan utama untuk modul 2.
    public function pemrosesanUtamaModul_2()
    {
        //Buat folder-folder output
        mkdir(Umum::FOLDER_HASIL_2 . $_SESSION["timestamp"] . Umum::FOLDER_TABEL_FREQ_EDU,0777,true);
        mkdir(Umum::FOLDER_HASIL_2 . $_SESSION["timestamp"] . Umum::FOLDER_TABEL_FREQ_STC,0777,true);
        mkdir(Umum::FOLDER_HASIL_2 . $_SESSION["timestamp"] . Umum::FOLDER_LIST_TOKEN,0777,true);

        //proses list token
        $this->pemrosesanListToken_semuaFile();
        TulisHasil::tuliskanListToken_modul2($this->array_token_seluruh_file);

        //lakukan pemrosesan utama
        $array_hasil_tf_edu = array();
        $array_hasil_tf_sentence = array();

        for($j=0;$j<count($this->array_objek_file_unit);$j++)           // proses untuk tiap file unit, terhadap fileAcuan.
        {
            $hasil_edu = $this->pemrosesanTabelFrekuensi_edu_semuaFile($this->array_objek_file_unit[$j]);
            $hasil_sentence = $this->pemrosesanTabelFrekuensi_sentence_semuaFile($this->array_objek_file_unit[$j]);

            $array_hasil_tf_edu = array_merge($array_hasil_tf_edu, $hasil_edu);
            $array_hasil_tf_sentence = array_merge($array_hasil_tf_sentence, $hasil_sentence);
        }
        TulisHasil::tuliskanTabelFrekuensi(Umum::FOLDER_HASIL_2 . $_SESSION["timestamp"] . Umum::FOLDER_TABEL_FREQ_EDU . Umum::NAMAFILE_TABEL_FREQ_MOD2_EDU, $array_hasil_tf_edu);         // tulis hasil pemrosesan tabel frekuensi untuk edu.
        TulisHasil::tuliskanTabelFrekuensi(Umum::FOLDER_HASIL_2 . $_SESSION["timestamp"] . Umum::FOLDER_TABEL_FREQ_STC . Umum::NAMAFILE_TABEL_FREQ_MOD2_STC, $array_hasil_tf_sentence);    // tulis hasil pemrosesan tabel frekuensi untuk sentence.
    }

    //private function pemrosesanListToken_semuaFile() :: pemrosesan list token untuk semua file unit.
    private function pemrosesanListToken_semuaFile()
    {
        $this->array_token_seluruh_file = array();
        $total1 = count($this->array_objek_file_unit);
        for($i=0;$i<$total1;$i++)
        {
            $total2 = count($this->array_objek_file_unit[$i]->array_token);
//echo "[abouDebug]Jumlah Token Unit $i = $total2 <br/>";
            for($j=0;$j<$total2;$j++)
            {
                Pembantu::masukkanElemenUnikTerurut($this->array_token_seluruh_file, $this->array_objek_file_unit[$i]->array_token[$j],$this->array_stopwords,$this->array_index_alph_stopwords);
            }
        }
    }

    //private function pemrosesanTabelFrekuensi_edu_semuaFile($objek_unit) :: pemrosesan tabel frekuensi untuk satu unit file berdasarkan edu, acuan yang digunakan adalah acuan token seluruh file.
    private function pemrosesanTabelFrekuensi_edu_semuaFile($objek_unit)
    {
        $array_hasil = array();
        $awal = 0;
        $akhir = 0;
        $index = 0;
        while($index < count($objek_unit->array_jml_kata_per_edu))
        {
            $akhir = $awal + $objek_unit->array_jml_kata_per_edu[$index];

            $hasil_tmp = array();
            for($i = 0; $i < count($this->array_token_seluruh_file);$i++)
            {
                $jumlah_kata = Pembantu::hitungKata2($this->array_token_seluruh_file[$i],$objek_unit->array_token,$awal,$akhir);
                array_push($hasil_tmp, $jumlah_kata);
            }
            array_push($array_hasil, $hasil_tmp);
            $awal = $akhir;
            $index++;
        }
//echo "[abouDebug]HasilPemrosesan " . $objek_unit->nama . " : " . json_encode($array_hasil) . "<br/><br/>";
        return $array_hasil;
    }

    //private function pemrosesanTabelFrekuensi_sentence_semuaFile($objek_unit) :: pemrosesan tabel frekuensi untuk satu unit file berdasarkan sentence, acuan yang digunakan adalah acuan token seluruh file.
    private function pemrosesanTabelFrekuensi_sentence_semuaFile($objek_unit)
    {
        $array_hasil = array();
        $awal = 0;
        $akhir = 0;
        $index = 0;
        while($index < count($objek_unit->array_jml_kata_per_sentence))
        {
            $akhir = $awal + $objek_unit->array_jml_kata_per_sentence[$index];

            $hasil_tmp = array();
            for($i = 0; $i < count($this->array_token_seluruh_file);$i++)
            {
                $jumlah_kata = Pembantu::hitungKata2($this->array_token_seluruh_file[$i],$objek_unit->array_token,$awal,$akhir);
                array_push($hasil_tmp, $jumlah_kata);
            }
            array_push($array_hasil, $hasil_tmp);
            $awal = $akhir;
            $index++;
        }
//echo "[abouDebug]HasilPemrosesan " . $objek_unit->nama . " : " . json_encode($array_hasil) . "<br/><br/>";
        return $array_hasil;
    }

    /***********/
    /* MODUL 3 */
    /***********/

    //private function persiapkanModul3() :: fungsi untuk mempersiapkan semua data untuk pemrosesan pada modul 3. Mengisi objek _unit_IUF.
    private function persiapkanModul3()
    {
        Pembantu::muatListTokenInputModul3($this->objek_unit_IUF);      //muat file list token.
        $this->muatNamaFileInputModul3();
        for($i=0;$i<count($this->array_nama_file_input_mod3);$i++)
        {
            Pembantu::muatTabelFrekuensiInputModul3(Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_TF . $this->array_nama_file_input_mod3[$i], $this->objek_unit_IUF);
        }
    }

    //private function muatNamaFileInputModul3() :: ambil semua nama file unit dari folder input modul 3.
    private function muatNamaFileInputModul3()
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_TF))
        {
            echo "Data tidak lengkap.. Persiapkan folder input tabel frekuensi untuk modul 3 : '".Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_TF."' <br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_3 . Umum::FOLDER_MASUKAN_MODUL_3_TF);
        //$this->objek_unit_IUF['array_list_token'] = array();
        $this->objek_unit_IUF['array_frekuensi_unit'] = array();
        $this->objek_unit_IUF['tipe'] = "";

        if($handle)
        {
            $i = 0;
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != "..")
                {
                    if($this->objek_unit_IUF['tipe'] == '')     //menentukan tipe file.
                    {
                        if(!strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_TABEL_FREQ_STC)), Umum::AKHIRAN_TABEL_FREQ_STC))
                        {
                            $this->objek_unit_IUF['tipe'] = 'SD';
                            $_SESSION["per_folder"] = false; //aboubakr 27012013 : no need to use any question in module 3.
                        }
                        else if(!strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_TABEL_FREQ_EDU)), Umum::AKHIRAN_TABEL_FREQ_EDU))
                        {
                            $this->objek_unit_IUF['tipe'] = 'ED';
                            $_SESSION["per_folder"] = false; //aboubakr 27012013 : no need to use any question in module 3.
                        }
                        else
                        {
                            $this->objek_unit_IUF['tipe'] = 'DD';
                            $_SESSION["per_folder"] = true; //aboubakr 27012013 : no need to use any question in module 3.
                        }
                    }
                    array_push($this->array_nama_file_input_mod3, $file);    // yang disimpan hanya bagian nama utamanya saja, hilangkan
                    $i++;
                }
            }
            closedir($handle);
        }
    }

    //private function hitungNilaiIUF()
    private function hitungNilaiIUF($unitDokumen)
    {
        $parameter1 = 0;
//echo json_encode($this->objek_unit_IUF) . "<br/>";
        if($unitDokumen)
        {
            $parameter1 = count($this->objek_unit_IUF['array_frekuensi_unit']);
        }
        else
        {
            $parameter1 = 0;
            for($i=0;$i<count($this->objek_unit_IUF['array_frekuensi_unit']);$i++)
            {
                $parameter1 += count($this->objek_unit_IUF['array_frekuensi_unit'][$i]);
            }
        }
//echo "<br/>Parameter 1 Rumus IUF :: $parameter1 <br/>"; //aboubakrTmp
        $this->array_hasil_IUF = array_fill(0, count($this->objek_unit_IUF['array_list_token']), 0);
        $this->objek_unit_IUF['array_total'] = array_fill(0, count($this->objek_unit_IUF['array_list_token']), 0);
        $tmp_array_status_terhitung = array_fill(0, count($this->objek_unit_IUF['array_list_token']), false);
        for($i=0; $i<count($this->objek_unit_IUF['array_frekuensi_unit']);$i++)                 // untuk tiap file
        {
//echo "<br/>Total Unit File ke-" . ($i+1) . " : " . count($this->objek_unit_IUF['array_frekuensi_unit'][$i]); //aboubakrTmp
            $tmp_array_status_terhitung = array_fill(0, count($this->objek_unit_IUF['array_list_token']), false);

            for($j=0;$j<count($this->objek_unit_IUF['array_frekuensi_unit'][$i]);$j++)          // untuk tiap unit
            {
                for($k=0;$k<count($this->objek_unit_IUF['array_frekuensi_unit'][$i][$j]);$k++)  // untuk tiap elemen array unit
                {
                    $this->objek_unit_IUF['array_total'][$k] += $this->objek_unit_IUF['array_frekuensi_unit'][$i][$j][$k];
                    
                    if(($this->objek_unit_IUF['array_frekuensi_unit'][$i][$j][$k] > 0) &&
                            (($unitDokumen && !$tmp_array_status_terhitung[$k]) || (!$unitDokumen)))
                    {
//echo "<br/>BonuS " . $this->objek_unit_IUF['array_list_token'][$k] . " <br/>"; //aboubakrTmp
                        $this->array_hasil_IUF[$k]++;   // Untuk menghitung, apakah dalam suatu EDU atau Sentence atau Dokumen, sudah terhitung.
                        $tmp_array_status_terhitung[$k] = true;            // nyalakan flag terhitung. jangan dihitung lagi untuk file berikutnya.
                    }
                }
            }
        }
//echo "<br/>Total = " . count($this->objek_unit_IUF['array_frekuensi_unit']) . " :: " . json_encode($this->objek_unit_IUF['array_frekuensi_unit']);

        $parameter2 = array();
        $parameter2 = $this->array_hasil_IUF;     // simpan parameter kedua, jumlah unit
        $panjang = count($parameter2);
//echo "[abouDebug]" . json_encode($this->objek_unit_IUF['array_total']) . "<br/>";
//echo "<br/>Total hasil = " . count($this->array_hasil_IUF) . ", Hasil perhitungan TF :: " . json_encode($this->array_hasil_IUF) . "<br/>"; //aboubakrTmp
        for($i=0;$i<$panjang; $i++)
        {
            if($_SESSION['tambah_satu'])        // SESSION seharusnya diset dari halaman depan, berdasarkan checkbox pada tampilan.
            {
//echo $i . " = " . $parameter1 . " / " . ($parameter2[$i]+1) . "<br/>" ; //aboubakrTmp
                $this->array_hasil_IUF[$i] = log10($parameter1 / ($parameter2[$i]+1));  // rumus IUF, sudah sesuai dengan pilihan tipe: edu, sentence, atau dokumen.
            }
            else
            {
                $this->array_hasil_IUF[$i] = log10($parameter1 / $parameter2[$i]);      // rumus IUF, sudah sesuai dengan pilihan tipe: edu, sentence, atau dokumen.
            }

            $this->array_hasil_IUF[$i] = round($this->array_hasil_IUF[$i], 4);
        }
//echo "<br/>Total hasil = " . count($this->array_hasil_IUF) . ", Hasil perhitungan IUF :: <br/>" . json_encode($this->array_hasil_IUF); //aboubakrTmp

//Penulisan Hasil ke file keluaran.
        TulisHasil::tuliskanLemma_TF_IUF_modul3(Umum::FOLDER_HASIL_3 . $_SESSION["timestamp"]. "/" . Umum::NAMAFILE_HASIL_MOD3_LEMMA_IUF, $this->objek_unit_IUF['array_list_token'], $this->array_hasil_IUF);
        TulisHasil::tuliskanIUF_modul3(Umum::FOLDER_HASIL_3 . $_SESSION["timestamp"]. "/" . Umum::NAMAFILE_HASIL_MOD3_IUF, $this->array_hasil_IUF);
        TulisHasil::tuliskanLemma_TF_IUF_modul3(Umum::FOLDER_HASIL_3 . $_SESSION["timestamp"]. "/" . Umum::NAMAFILE_HASIL_MOD3_LEMMA_TF, $this->objek_unit_IUF['array_list_token'], $this->objek_unit_IUF['array_total']);

        TulisHasil::tuliskanHasil_modul3(Umum::FOLDER_HASIL_3 . $_SESSION["timestamp"]. "/",$this->array_hasil_IUF,$this->objek_unit_IUF,$this->array_nama_file_input_mod3);
    }

    //public function pemrosesanUtamaModul_3($unitDokumen) :: pemrosesan utama untuk modul 3.
    public function pemrosesanUtamaModul_3($unitDokumen)
    {
        //Buat folder-folder output
        mkdir(Umum::FOLDER_HASIL_3 . $_SESSION["timestamp"] ,0777,true);
        $this->hitungNilaiIUF($unitDokumen);
    }

    /***********/
    /* MODUL 4 */
    /***********/

    //private function muatNamaFileInputModul4() :: ambil semua nama file unit dari folder input modul 3.
    private function muatNamaFileInputModul4()
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_TF))
        {
            echo "Data tidak lengkap.. Persiapkan folder input tabel frekuensi untuk modul 4 : '".Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_TF."' <br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_TF);
        $this->objek_unit_IUF['array_frekuensi_unit'] = array();
        $this->objek_unit_IUF['tipe'] = "";

        if($handle)
        {
            $i = 0;
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != "..")
                {
                    if($this->objek_unit_IUF['tipe'] == '')     //menentukan tipe file.
                    {
                        if(!strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_TABEL_FREQ_STC)), Umum::AKHIRAN_TABEL_FREQ_STC))
                        {
                            $this->objek_unit_IUF['tipe'] = 'SD';
                        }
                        else if(!strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_TABEL_FREQ_EDU)), Umum::AKHIRAN_TABEL_FREQ_EDU))
                        {
                            $this->objek_unit_IUF['tipe'] = 'ED';
                        }
                    }
                    array_push($this->array_nama_file_input_mod4, $file);    // yang disimpan hanya bagian nama utamanya saja, hilangkan
                    $i++;
                }
            }
            closedir($handle);
//echo print_r($this->objek_unit_IUF);
//exit(0);
        }
    }

    //private function persiapkanModul4() :: memuat semua file yang dibutuhkan dan menyimpannya ke dalam struktur.
    private function persiapkanModul4()
    {
        Pembantu::muatNilaiIUFModul4($this->array_IUF_input_mod4);
//echo json_encode($this->array_IUF_input_mod4);
        $this->muatNamaFileInputModul4();
        $total = count($this->array_nama_file_input_mod4);
        for($i=0;$i<$total;$i++)
        {
            Pembantu::muatTabelFrekuensiInputModul4(Umum::FOLDER_MASUKAN_MODUL_4 . Umum::FOLDER_MASUKAN_MODUL_4_TF . $this->array_nama_file_input_mod4[$i], $this->objek_unit_IUF);
        }
    }

    //private function hitungNormalWeight() :: menghitung nilai normal weight.
    private function hitungNormalWeight()
    {
        TulisHasil::tuliskanHasil_modul4(Umum::FOLDER_HASIL_4 . $_SESSION["timestamp"]. "/", $this->array_IUF_input_mod4, $this->objek_unit_IUF,$this->array_nama_file_input_mod4);
    }
    
    //public function pemrosesanUtamaModul_4() :: pemrosesan utama untuk modul 4.
    public function pemrosesanUtamaModul_4()
    {
        //Buat folder-folder output
        mkdir(Umum::FOLDER_HASIL_4 . $_SESSION["timestamp"] ,0777,true);
        $this->hitungNormalWeight();
    }


    /***********/
    /* MODUL 5 */
    /***********/
    //private function muatNamaFileTargetModul5() :: ambil semua nama file unit dari folder input modul 5.
    private function muatNamaFileTargetModul5()
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_5 . Umum::FOLDER_MASUKAN_MODUL_5_TARGET))
        {
            echo "Data tidak lengkap.. Persiapkan folder input target untuk modul 5 : '".Umum::FOLDER_MASUKAN_MODUL_5 . Umum::FOLDER_MASUKAN_MODUL_5_TARGET."' <br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_5 . Umum::FOLDER_MASUKAN_MODUL_5_TARGET);
        $this->array_nama_file_target_mod5 = array();

        if($handle)
        {
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != "..")
                {
                    $panjangNamaFile = strlen($file);
                    array_push($this->array_nama_file_target_mod5, substr($file, 0, $panjangNamaFile-4));    // yang disimpan hanya bagian nama utamanya saja, hilangkan extension.
                }
            }
            closedir($handle);
        }
    }

    //private function persiapkanModul5() :: mempersiapkan modul 5, muat semua array.
    private function persiapkanModul5()
    {
        Pembantu::muatArrayAcuanModul5($this->array_nama_file_acuan_mod5,$this->array_nilai_acuan_mod5,$this->tipeUnit_modul5_6);
        $this->muatNamaFileTargetModul5();
        $totalFileTarget = count($this->array_nama_file_target_mod5);
        for($i = 0; $i < $totalFileTarget; $i++)
        {
            Pembantu::muatArrayTargetModul5(Umum::FOLDER_MASUKAN_MODUL_5 . Umum::FOLDER_MASUKAN_MODUL_5_TARGET . $this->array_nama_file_target_mod5[$i] . ".txt",$this->array_nilai_target_mod5);
        }
    }

    //private function perantaraHitungSimilarityScore($array1,$array2) :: fungsi perantara perhitungan similarityScore
    private function perantaraHitungSimilarityScore($array1,$array2)
    {
        $hasil = 0;
        $total1 = count($array1);
        $total2 = count($array2);

        $parameter1 = 0;      // menampung parameter pertama.
        $parameter2_1 = 0;    // bagian pertama, sigma kuadrat Q --> lihat rumus pada dokumen.
        $parameter2_2 = 0;    // bagian kedua, sigma kuadrat P --> lihat rumus pada dokumen.
        $parameter2 = 0;      // menampung parameter kedua.

        if($total1 != $total2)
        {
            echo "Jumlah baris tidak sinkron!!<br/>";
            return null;
        }

        for($i=0; $i < $total1; $i++)   // Hitung parameter1, simpan di array.
        {
//echo $array1[$i] . " - " . $array2[$i] . "<br/>";
            $parameter1 += ($array1[$i] * $array2[$i]);
            $parameter2_1 += ($array1[$i] * $array1[$i]);
            $parameter2_2 += ($array2[$i] * $array2[$i]);
        }
//echo $parameter2_1 . " - " . $parameter2_2 . "<br/>";
        //parameter1 sudah siap. parameter2 butuh pemrosesan.
        $parameter2 = $parameter2_1 * $parameter2_2;
        $parameter2 = sqrt($parameter2);

        if($_SESSION['tambah_satu'])
        {
            $hasil = $parameter1 / ($parameter2+1);
        }
        else
        {
            $hasil = $parameter1 / $parameter2;
        }

        $hasil = round($hasil, 4);
//echo "Hasil = " . $hasil . "<br/>";
        return $hasil;
    }

    //private function hitungSimilarityScore($arrayQ,$arrayUnit) :: menghitung similarity score antara array acuan ($arrayQ) dengan array unit. $arrayQ dan $arrayUnit adalah array of array.
    private function hitungSimilarityScore($arrayQ,$arrayUnit)
    {
        $totalQ = count($arrayQ);
        $array_hasil = array();
        for($i = 0;$i < $totalQ; $i++)      // untuk tiap baris dalam arrayQ (array acuan).
        {
echo "<br/>Baris ke-$i array acuan <br/>";
            $array_hasil[$i] = array();
            $totalUnit = count($arrayUnit);
            for($j = 0;$j < $totalUnit; $j++)
            {
                $array_hasil[$i][$j] = $this->perantaraHitungSimilarityScore($arrayQ[$i], $arrayUnit[$j]);
echo "arrayAcuan - $i = " . json_encode($arrayQ[$i]) . " vs " . "arrayTarget - $j = " . json_encode($arrayUnit[$j]) . " ==> " . $array_hasil[$i][$j] . "<br/>";
            }
        }
echo "Hasil Similarity Score = " . json_encode($array_hasil) . "<br/>";
        return $array_hasil;
    }

    //public function pemrosesanUtamaModul_5() :: pemrosesan utama untuk modul 5.
    public function pemrosesanUtamaModul_5()
    {
        //Buat folder-folder output
        mkdir(Umum::FOLDER_HASIL_5 . $_SESSION["timestamp"] ,0777,true);
        $totalFileTarget = count($this->array_nilai_target_mod5);
        
        for($i=0; $i<$totalFileTarget;$i++)
        {
echo "File $i :: ". $this->array_nama_file_target_mod5[$i] ."<br/>";
            $tmpArray = $this->hitungSimilarityScore($this->array_nilai_acuan_mod5, $this->array_nilai_target_mod5[$i]);
            array_push($this->array_hasil_similarity_score,$tmpArray);
echo "==========================<br/><br/>";
        }
        TulisHasil::tuliskanHasil_modul5($this->array_nama_file_acuan_mod5[0], $this->array_nama_file_target_mod5, $this->array_hasil_similarity_score, $this->tipeUnit_modul5_6);
    }


    /***********/
    /* MODUL 6 */
    /***********/
    //private function muatNamaFileInputModul6()
    private function muatNamaFileInputModul6()
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_STRUNIT))
        {
            echo "Data tidak lengkap.. Persiapkan folder input modul 6 : '".Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_STRUNIT."' <br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_STRUNIT);
        $this->array_nama_file_unit_mod6 = array();
        
        if($handle)
        {
            $i = 0;
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != ".." && !strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_NERPOS_FILE)), Umum::AKHIRAN_NERPOS_FILE))
                {
                    array_push($this->array_nama_file_unit_mod6, substr($file, 0, 0-strlen(Umum::AKHIRAN_NERPOS_FILE)) );    // yang disimpan hanya bagian nama utamanya saja, hilangkan sisanya.
                    $i++;
                }
            }
            closedir($handle);
        }
    }

    //private function aturJumlahBarisFileAcuan() :: atur jumlahBarisFileAcuan, baca dari folder Acuan di folder masukan modul 6
    private function aturJumlahBarisFileAcuan()
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_ACUAN))
        {
            echo "Data tidak lengkap.. Persiapkan folder input similarity Score untuk modul 6 : '" . Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_SIMSCR . "' <br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_ACUAN);
        $fileAcuan = "";
        if($handle)
        {
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != "..")
                {
                    $fileAcuan = $file;
                    break;
                }
            }
            closedir($handle);
        }
        $this->jumlahBarisFileAcuan = Pembantu::hitungBarisFile(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_ACUAN . $fileAcuan);
    }

    //private function muatObjekFileUnitModul6() :: buat semua objek unit dari semua file yang ada di folder stringUnit modul 6.
    private function muatObjekFileUnitModul6()
    {
        for($i=0; $i < count($this->array_nama_file_unit_mod6); $i++)
        {
            $pilihanUnitString = "";
            switch($this->tipeUnit_modul5_6)
            {
                case 'ED':
                    $pilihanUnitString = Umum::UNIT_STRING_SINGLE_EDU;
                    break;
                case 'SD':
                    $pilihanUnitString = Umum::UNIT_STRING_SENTENCE;
                    break;
                default :
                case 'DD':
                    $pilihanUnitString = Umum::UNIT_STRING_DOKUMEN;
                    break;
            }
            $objek = new Unit($this->array_nama_file_unit_mod6[$i],Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_STRUNIT,$pilihanUnitString);
//echo "<br/>Hasil Objek Unit : " . json_encode($objek->array_unit_string) . "<br/><br/>";
            array_push($this->array_objek_unit_mod6, $objek);
        }
    }

    //private function muatNamaFileInputSimScoreModul6() :: ambil semua nama file unit dari folder input modul 6.
    private function muatNamaFileInputSimScoreModul6()
    {
        if(!is_dir(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_SIMSCR))
        {
            echo "Data tidak lengkap.. Persiapkan folder input similarity Score untuk modul 6 : '" . Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_SIMSCR . "' <br/>";
            exit(0);
        }

        $handle = opendir(Umum::FOLDER_MASUKAN_MODUL_6 . Umum::FOLDER_MASUKAN_MODUL_6_SIMSCR);
        $this->array_nama_file_sim_scr_mod6 = array();
        $this->tipeUnit_modul5_6 = '';

        if($handle)
        {
            $i = 0;
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != "..")
                {
                    if($this->tipeUnit_modul5_6 == '')     //menentukan tipe file.
                    {
                        if(!strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_INPUT_MOD6_STC)), Umum::AKHIRAN_INPUT_MOD6_STC))
                        {
                            $this->tipeUnit_modul5_6 = 'SD';
                        }
                        else if(!strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_INPUT_MOD6_EDU)), Umum::AKHIRAN_INPUT_MOD6_EDU))
                        {
                            $this->tipeUnit_modul5_6 = 'ED';
                        }
                        else
                        {
                            $this->tipeUnit_modul5_6 = 'DD';
                        }
                    }
                    array_push($this->array_nama_file_sim_scr_mod6, $file);
                    $i++;
                }
            }
            closedir($handle);
        }
        sort($this->array_nama_file_sim_scr_mod6);  //aboubakr 27012013 : need to sort in linux.
//echo "[aboubakrTmp]SimScore FILE LIST : <br/>" . json_encode($this->array_nama_file_sim_scr_mod6) . "<br/>";//aboubakrTmp
//exit(0);//aboubakrTmp
    }

    //private function cekKelengkapanFileMasukanModul6() :: Mengecek kelengkapan file masukan modul 6.
    private function cekKelengkapanFileMasukanModul6()
    {
        echo "Total File Input : " . count($this->array_nama_file_unit_mod6) . "<br/>";
        echo "Total File SimilarityScore : " . count($this->array_nama_file_sim_scr_mod6) . "<br/>";
        $totalFileInput = count($this->array_nama_file_unit_mod6);
        $totalFileSimScore = count($this->array_nama_file_sim_scr_mod6);

/*      aboubakr 27012013 : Tidak perlu beri peringatan untuk jumlah input file yg beda.
        if($totalFileSimScore > $totalFileInput)
        {
            echo "<span style='color:red;'>Peringatan, Jumlah File Input Tidak Sama !! File dalam folder '".Umum::FOLDER_MASUKAN_MODUL_6_SIMSCR."', Berjumlah lebih banyak.</span><br/>";
        }
        else if($totalFileSimScore < $totalFileInput)
        {
            echo "<span style='color:red;'>Peringatan, Jumlah File Input Tidak Sama !! File dalam folder '".Umum::FOLDER_MASUKAN_MODUL_6_STRUNIT."', Berjumlah lebih banyak.</span><br/>";
        }
        else
        {
            echo "<span style='color:green;'>Jumlah File Input Sama.</span><br/>";
        }
*/
        echo "List Semua Nama File Input : <br/>";
        echo "<div style='overflow-y:scroll; max-height:300px; width: 50%; border:double;'><table><tr><th>Unit String</th><th>Similarity Score</th></tr><tr><td valign='top'><table border='1'>";
        for($i=0; $i < $totalFileInput; $i++)
        {
            echo "<tr><td>" . $this->array_nama_file_unit_mod6[$i] . "</td></tr>";
        }
        echo "</table></td><td valign='top'><table border='1'>";
        for($i=0; $i < $totalFileSimScore; $i++)
        {
            echo "<tr><td>" . $this->array_nama_file_sim_scr_mod6[$i] . "</td></tr>";
        }
        echo "</table></td></tr></table></div>";

        // Cari Kesalahan
        $indexAkhir = $totalFileInput*$this->jumlahBarisFileAcuan;           //set Index Akhir
        if($totalFileSimScore > $totalFileInput*$this->jumlahBarisFileAcuan)
        {
            $indexAkhir = $totalFileSimScore;
        }

        echo "<br/>Cek Kelengkapan File Input...<br/><div style='overflow-y:scroll;max-height:300px;border:double;'>";
        for($i=0; $i < $indexAkhir; $i++)
        {
            $tmpArr = explode("__",$this->array_nama_file_sim_scr_mod6[$i]);
            $namaFileIntiSimScore = $tmpArr[1];
            $tmpArr = explode("_LS_",$namaFileIntiSimScore);
            $namaFileIntiSimScore = $tmpArr[1];
            $tmpArr = explode("-tf",$namaFileIntiSimScore);
            $namaFileIntiSimScore = $tmpArr[0];
//echo "[aboubakrTmp]DEBUG 1 : " . $namaFileIntiSimScore . "<br/>"; //aboubakrTmp
//echo "[aboubakrTmp]DEBUG 2 : " . $this->array_nama_file_unit_mod6[$i/$this->jumlahBarisFileAcuan] . "<br/>";  //aboubakrTmp
            if($namaFileIntiSimScore == $this->array_nama_file_unit_mod6[$i/$this->jumlahBarisFileAcuan])
            {
                echo "<span style='color:green;'> '" . $this->array_nama_file_unit_mod6[$i/$this->jumlahBarisFileAcuan] . "'</span>---<span style='color:green;'>'" . $this->array_nama_file_sim_scr_mod6[$i] . "' </span><br/>";
            }
            else
            {
                echo "<span style='color:red;'> '" . $this->array_nama_file_unit_mod6[$i/$this->jumlahBarisFileAcuan] . "'</span>---<span style='color:red;'>'" . $this->array_nama_file_sim_scr_mod6[$i] . "' </span><br/>";
                echo "<span style='color:red;'>Kesalahan ditemukan... Silahkan lengkapi file pada lokasi yang bersangkutan... Program tidak dapat dilanjutkan.</span>";
                die();  // Program tidak akan diteruskan jika menemukan kesalahan.
            }
        }
        echo "</div><span style='color:blue'><b>Pengecekan Sukses... File Input Valid !!!</b></span>";
    }

    //private function cekTotalUnitSimScore() :: Mengecek jumlah total unit dan similarity score yang sudah dimuat.
    private function cekTotalUnitSimScore()
    {
        $totalFileSimScore = count($this->array_nama_file_sim_scr_mod6);
        echo "<br/>Cek Total Unit dan Jumlah Nilai Similarity Score : <br/>";
        echo "<div style='overflow-y:scroll;max-height:300px;border:double;'>";
        for($i=0; $i < $totalFileSimScore; $i++)
        {
            echo "<br/>Dokumen - " . floor(($i/$this->jumlahBarisFileAcuan)+1) . "<br/>";
            if($this->tipeUnit_modul5_6 == 'ED')
            {
                echo "Total SimScore Unit #" . (($i%$this->jumlahBarisFileAcuan)+1) . " = " . count($this->array_nilai_similarity_score[$i]) . " ----- ";
                echo "Total EDU #" . (($i%$this->jumlahBarisFileAcuan)+1) . " = " . count($this->array_objek_unit_mod6[$i/$this->jumlahBarisFileAcuan]->array_jml_kata_per_edu) . "<br/>";
            }
            else if($this->tipeUnit_modul5_6 == 'SD')
            {
                echo "Total SimScore #" . (($i%$this->jumlahBarisFileAcuan)+1) . " = " . count($this->array_nilai_similarity_score[$i]) . " ----- ";
                echo "Total Sentence #" . (($i%$this->jumlahBarisFileAcuan)+1) . " = " . count($this->array_objek_unit_mod6[$i/$this->jumlahBarisFileAcuan]->array_jml_kata_per_sentence) . "<br/>";
                echo "<br/>";
            }
            else    // doc
            {
                echo "Total SimScore #" . (($i%$this->jumlahBarisFileAcuan)+1) . " = " . count($this->array_nilai_similarity_score[$i]) . " ----- ";
                echo "Total EDU #" . (($i%$this->jumlahBarisFileAcuan)+1) . " = " . count($this->array_objek_unit_mod6[$i/$this->jumlahBarisFileAcuan]->array_jml_kata_per_edu) . "<br/>";
                echo "<br/>";
            }
        }
        echo "<br/></div>";
    }

    //private function muatNilaiSimilarityScore() :: Memuat 
    private function muatNilaiSimilarityScore()
    {
        $totalFileSimScore = count($this->array_nama_file_sim_scr_mod6);
        $this->array_nilai_similarity_score = array();
        for($i=0; $i < $totalFileSimScore; $i++)
        {
            $tmpArr = Pembantu::muatNilaiSimilarityScore($this->array_nama_file_sim_scr_mod6[$i]);
            array_push($this->array_nilai_similarity_score, $tmpArr);
        }
//echo "<br/>Similarity Score : " . json_encode($this->array_nilai_similarity_score) . "<br/>";
    }

    //private function muatArrayHasilSimilarityScoreTerurut() :: membuat array yang berisi hasil keluaran modul 6
    private function muatArrayHasilSimilarityScoreTerurut($gen_individual_files)
    {
        $totalDokumenSimScore = count($this->array_nilai_similarity_score);
        $this->array_hasil_sim_score_urut = array();
        $this->array_hasil_sim_score_per_file = array();
        $totalElemenHasil = 0;

        $this->array_hasil_sim_score_urut['simScore'] = array();
        $this->array_hasil_sim_score_urut['docIdx'] = array();
        $this->array_hasil_sim_score_urut['unitIdx'] = array();
        $this->array_hasil_sim_score_urut['unitStr'] = array();
        for($i = 0; $i < $totalDokumenSimScore; $i++)
        {
            if($gen_individual_files)
            {
                $this->array_hasil_sim_score_per_file[$i] = array();
                $this->array_hasil_sim_score_per_file[$i]['simScore'] = array();
                $this->array_hasil_sim_score_per_file[$i]['docIdx'] = array();
                $this->array_hasil_sim_score_per_file[$i]['unitIdx'] = array();
                $this->array_hasil_sim_score_per_file[$i]['unitStr'] = array();
            }

            $totalSimScore = count($this->array_nilai_similarity_score[$i]);
            for($j=0;$j<$totalSimScore;$j++)
            {
                $this->array_hasil_sim_score_urut['simScore'][$totalElemenHasil] = $this->array_nilai_similarity_score[$i][$j]; //nilai similarity score.
                $this->array_hasil_sim_score_urut['docIdx'][$totalElemenHasil] = floor(($i/$this->jumlahBarisFileAcuan)+1);      // document index. //aboubakr 27012013 : ubah document index, mengacu ke urutan di StringUnit, bukan urutan dokumen di SimScore. --> sebelumnya ($i+1)
                $this->array_hasil_sim_score_urut['unitIdx'][$totalElemenHasil] = ($j+1);      // unit index.
                $this->array_hasil_sim_score_urut['unitStr'][$totalElemenHasil] = implode(" ", $this->array_objek_unit_mod6[$i/$this->jumlahBarisFileAcuan]->array_unit_string[$j]);
                
                if($gen_individual_files)
                {
                    //save results per file.
                    $this->array_hasil_sim_score_per_file[$i]['simScore'][$j] = $this->array_hasil_sim_score_urut['simScore'][$totalElemenHasil];
                    $this->array_hasil_sim_score_per_file[$i]['docIdx'][$j] = $this->array_hasil_sim_score_urut['docIdx'][$totalElemenHasil];
                    $this->array_hasil_sim_score_per_file[$i]['unitIdx'][$j] = $this->array_hasil_sim_score_urut['unitIdx'][$totalElemenHasil];
                    $this->array_hasil_sim_score_per_file[$i]['unitStr'][$j] = $this->array_hasil_sim_score_urut['unitStr'][$totalElemenHasil];                    
                }

                $totalElemenHasil++;
            }
            if($gen_individual_files)
            {
                array_multisort($this->array_hasil_sim_score_per_file[$i]['simScore'], SORT_DESC, $this->array_hasil_sim_score_per_file[$i]['docIdx'], $this->array_hasil_sim_score_per_file[$i]['unitIdx'], $this->array_hasil_sim_score_per_file[$i]['unitStr']);                
            }
        }

        array_multisort($this->array_hasil_sim_score_urut['simScore'], SORT_DESC, $this->array_hasil_sim_score_urut['docIdx'], $this->array_hasil_sim_score_urut['unitIdx'], $this->array_hasil_sim_score_urut['unitStr']);
    }

    //private function pemrosesanUtamaModul_6() :: mempersiapkan modul 6, memuat semua array, tulis hasil.
    private function pemrosesanUtamaModul_6($gen_individual_files)
    {
        $this->aturJumlahBarisFileAcuan();
        $this->muatNamaFileInputModul6();
        $this->muatNamaFileInputSimScoreModul6();
        $this->muatObjekFileUnitModul6();
        $this->cekKelengkapanFileMasukanModul6();
        //Apabila telah lewat dari tahap pengecekan, berarti file input telah valid.

        $this->muatNilaiSimilarityScore();
        $this->cekTotalUnitSimScore();

        //Proses Hasil.
        $this->muatArrayHasilSimilarityScoreTerurut($gen_individual_files);
        $namaFileHasil = $this->array_nama_file_sim_scr_mod6[0];    //ambil salah satu file, ambil bagian utamanya untuk nama file keluaran modul 6.
        //#1_1_Q1_LS_Dok1-tf-ED___Q1_LS_Dok2-tf-ED-SimS-ED
        $tmpArr = explode("__", $namaFileHasil);
        $tmpArr[1] = explode('-SimS-',$tmpArr[1]);                  // mengambil bagian P.
        //$namaFileHasil = $tmpArr[0] . "_" . $tmpArr[1][0] . "-SimSsrt-" . $this->tipeUnit_modul5_6 . ".txt";
        $namaFileHasil = "ALL-SimSsrt-" . $this->tipeUnit_modul5_6 . ".txt";
        TulisHasil::tuliskanHasil_modul6($this->array_hasil_sim_score_urut, $namaFileHasil);

        if($gen_individual_files)
        {
            for($i=0;$i<count($this->array_nama_file_sim_scr_mod6);$i++)
            {
                $namaFileHasil = $this->array_nama_file_sim_scr_mod6[$i];
                $tmpArr = explode("__", $namaFileHasil);
                $namaFileHasil = $tmpArr[0] . "-SimSsrt-" . $this->tipeUnit_modul5_6 . ".txt";
                TulisHasil::tuliskanHasil_modul6($this->array_hasil_sim_score_per_file[$i], $namaFileHasil);
            }            
        }

        array_multisort($this->array_hasil_sim_score_urut['docIdx'], SORT_ASC, $this->array_hasil_sim_score_urut['simScore'], SORT_DESC, $this->array_hasil_sim_score_urut['unitIdx'], $this->array_hasil_sim_score_urut['unitStr']);
        $namaFileHasil = "Individual-SimSsrt-" . $this->tipeUnit_modul5_6 . ".txt";
        TulisHasil::tuliskanHasil_modul6($this->array_hasil_sim_score_urut, $namaFileHasil);

    }
}
?>
