<?php
include_once 'Umum.php';
include_once 'Pembantu.php';
/**
 * Unit adalah suatu class yang menunjukkan entitas sekelompok file news (EDU dan NERPOS).
 *
 * @author Abu Bakar Gadi
 */

class Unit {
    /*************/
    /* ATTRIBUTE */
    /*************/
    public $nama;                           // nama untuk file EDU dan NERPOS.
    public $array_jml_kata_per_edu;         // array jumlah kata untuk tiap EDU, sesuai urutan EDU.
    public $array_jml_kata_per_sentence;    // array jumlah kata untuk tiap sentence, sesuai urutan sentence.
    public $array_word;                     // array word, diambil dari file NERPOS, index ke 2 dari tabel NERPOS.
    public $array_word_ori;                 // array word original, berisi list semua word, tempat menyimpan list word asli, dipakai untuk mengembalikan $array_word ke kondisi asli setelah pemrosesan unit.
    public $array_token;                    // array of token (lemma), diambil dari file NERPOS, index ke 3 dari tabel NERPOS.
    public $array_token_ori;                // array token original, berisi list semua token, tempat menyimpan list token asli, dipakai untuk mengembalikan $array_token ke kondisi asli setelah pemrosesan unit.
    public $array_pos;                      // array of POS, diambil dari file NERPOS, index ke 6 dari tabel NERPOS.
    public $array_pos_unit;                 // array of POS setelah pemrosesan unit.
    public $array_ner;                      // array of NER, diambil dari file NERPOS, index ke 7 dari tabel NERPOS.
    public $array_ner_unit;                 // array of NER setelah pemrosesan unit.
    public $array_unit_string;              // array unit String setelah diproses.
    public $array_unit_string_word;         // array unit String setelah diproses, berisi Word, untuk ditulis di hasil, bukan untuk perhitungan.
    public $pilihanKriteria;                // pilihan kriteria.

    /*****************/
    /* FUNGSI-FUNGSI */
    /*****************/
    public function __construct($nama,$folder,$pilihanUnitString=false,$pilihanKriteria=false) {
        $this->nama                         = $nama;
        $this->array_jml_kata_per_edu       = array();
        $this->array_jml_kata_per_sentence  = array();
        $this->array_word                   = array();
        $this->array_word_ori               = array();
        $this->array_token                  = array();
        $this->array_token_ori              = array();
        $this->array_pos                    = array();
        $this->array_pos_unit               = array();
        $this->array_ner                    = array();
        $this->array_ner_unit               = array();
        $this->pilihanKriteria              = $pilihanKriteria;

        $this->persiapkanFileUnit($folder);
        if($pilihanUnitString !== false)      // jika bernilai false (nilai default), maka tidak dilakukan pemrosesan berdasar unit string pilihan. Jika tidak bernilai false, maka unit akan diproses berdasar unit string yang dipilih dan hasiln disimpan pada array $array_unit_string.
        {
            $this->array_unit_string = array();
            $this->array_unit_string_word = array();
            $this->persiapkanUnitString($pilihanUnitString);

            if($pilihanUnitString >= Umum::UNIT_STRING_DOUBLE_EDU)
            {
                if(!is_dir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_LIST_MULTIPLE_UNIT))
                {
                    mkdir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_LIST_MULTIPLE_UNIT,0777,true);    //buat folder keluaran 3.   //aboubakr 27012013 : tambah file keluaran, list unit untuk multiple EDU.
                }

                //echo "Unit string untuk file - $this->nama :: " . json_encode($this->array_unit_string) . "<br/>";
                echo "<span style='font-weight:bold;color:brown;font-size:large;'>=========Total untuk file $nama : " . count($this->array_unit_string) . " Unit.=========</span><br/><br/>";
                
                if($pilihanUnitString == Umum::UNIT_STRING_DOUBLE_EDU)
                {
                    $this->debugMultipleEDU(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_LIST_MULTIPLE_UNIT . "$nama-2EDU" . Umum::AKHIRAN_HASIL_SELEKSI_UNIT);
                }
                else
                {
                    $this->debugMultipleEDU(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_LIST_MULTIPLE_UNIT . "$nama-3EDU" . Umum::AKHIRAN_HASIL_SELEKSI_UNIT);
                }
            }
        }
    }

    public function persiapkanFileUnit($folder)
    {
        //Mengambil token dan nerpos.
        Pembantu::muatFileNERPOS(true,$folder . $this->nama . Umum::AKHIRAN_NERPOS_FILE,$this->array_word,$this->array_token,$this->array_pos,$this->array_ner,$this->array_word_ori,$this->array_token_ori);
        //Ambil jumlah kata untuk tiap unit
        Pembantu::muatFileEDU($folder . $this->nama . Umum::AKHIRAN_EDU_FILE, $this->array_jml_kata_per_edu, $this->array_jml_kata_per_sentence);
//echo '[abouDebug]' . $this->nama . ' COUNT EDU :: '.  json_encode($this->array_jml_kata_per_edu).'<br/>';
//echo '[abouDebug]' . $this->nama . ' COUNT SENTENCE :: '.  json_encode($this->array_jml_kata_per_sentence).'<br/>';
//echo '[abouDebug]' . $this->nama . ' WORDS :: '.  json_encode($this->array_word).'<br/>';
//echo '[abouDebug]' . $this->nama . ' LEMMA :: '.  json_encode($this->array_token).'<br/>';
//echo '[abouDebug]' . $this->nama . ' POS :: '.  json_encode($this->array_pos).'<br/>';
//echo '[abouDebug]' . $this->nama . ' NER :: '.  json_encode($this->array_ner).'<br/><br/>';
    }

    //private function persiapkanUnitString($pilihanUnitString) :: mengelompokkan unit berdasarkan unit string yang dipilih.
    /* Pilihan unit string :
       const UNIT_STRING_DOKUMEN           = 0;
       const UNIT_STRING_SENTENCE          = 1;
       const UNIT_STRING_SINGLE_EDU        = 2;
       const UNIT_STRING_DOUBLE_EDU        = 3;
       const UNIT_STRING_TRIPLE_EDU        = 4;
    */
    private function persiapkanUnitString($pilihanUnitString)
    {
        switch($pilihanUnitString)
        {
            case Umum::UNIT_STRING_DOKUMEN :    // sama seperti nilai array token.
                if(Pembantu::gunakanLemma($this->pilihanKriteria))
                {
                    $this->array_unit_string[0] = array();
                    $this->array_unit_string_word[0] = array();
                    if($this->pilihanKriteria & Umum::FILTER_POS_PRP)  //aboubakr 30-06-2013 : special handling untuk PRP, unit yang dipakai selalu word. Jika PRP masuk dalam pilihan kriteria, lakukan prosedur khusus.
                    {
                        $total = count($this->array_token);
                        for($i=0;$i<$total;$i++)
                        {
                            if(Pembantu::ambilNilaiTag($this->array_pos[$i]) == Umum::FILTER_POS_PRP)
                            {
                                array_push($this->array_unit_string[0], $this->array_word[$i]);
                                array_push($this->array_unit_string_word[0], $this->array_word[$i]);
                            }
                            else
                            {
                                array_push($this->array_unit_string[0], $this->array_token[$i]);
                                array_push($this->array_unit_string_word[0], $this->array_word[$i]);    //aboubakr 01-10-2013
                            }
                        }
                    }
                    else
                    {
                        $this->array_unit_string[0] = &$this->array_token;
                    }
                }
                else
                {
                    $this->array_unit_string[0] = array();
                    $this->array_unit_string[0] = &$this->array_word;
                }
                break;
            case Umum::UNIT_STRING_SENTENCE :
                $totalUnitSentence = count($this->array_jml_kata_per_sentence);
                $indexTokenSekarang = 0;
                $indexTokenAkhir = 0;
                for($i=0; $i < $totalUnitSentence; $i++)
                {
                    $this->array_unit_string[$i] = array();
                    $indexTokenAkhir = $indexTokenSekarang + $this->array_jml_kata_per_sentence[$i];
                    for($j = $indexTokenSekarang; $j < $indexTokenAkhir; $j++)
                    {
                        if(Pembantu::gunakanLemma($this->pilihanKriteria)
                                && (Pembantu::ambilNilaiTag($this->array_pos[$j]) != Umum::FILTER_POS_PRP))       //aboubakr 30-06-2013 : special handling untuk PRP, unit yang dipakai selalu word.
                        {
                            array_push($this->array_unit_string[$i], $this->array_token[$j]);           //masukkan token ke array unit string, pada posisi yang sesuai.
                        }
                        else
                        {
                            array_push($this->array_unit_string[$i], $this->array_word[$j]);            //masukkan word ke array unit string, pada posisi yang sesuai.
                        }
                    }
                    $indexTokenSekarang = $indexTokenAkhir;
                }
                break;
            case Umum::UNIT_STRING_SINGLE_EDU :
                $totalUnitEdu = count($this->array_jml_kata_per_edu);
                $indexTokenSekarang = 0;
                $indexTokenAkhir = 0;
                for($i=0; $i < $totalUnitEdu; $i++)
                {
                    $this->array_unit_string[$i] = array();
                    $indexTokenAkhir = $indexTokenSekarang + $this->array_jml_kata_per_edu[$i];
                    for($j = $indexTokenSekarang; $j < $indexTokenAkhir; $j++)
                    {
                        if(Pembantu::gunakanLemma($this->pilihanKriteria)
                                && (Pembantu::ambilNilaiTag($this->array_pos[$j]) != Umum::FILTER_POS_PRP))     //aboubakr 30-06-2013 : special handling untuk PRP, unit yang dipakai selalu word.
                        {
                            array_push($this->array_unit_string[$i], $this->array_token[$j]);    //masukkan token ke array unit string, pada posisi yang sesuai.
                        }
                        else
                        {
                            array_push($this->array_unit_string[$i], $this->array_word[$j]);    //masukkan word ke array unit string, pada posisi yang sesuai.
                        }
                    }
                    $indexTokenSekarang = $indexTokenAkhir;
                }
                break;
            case Umum::UNIT_STRING_DOUBLE_EDU :
            case Umum::UNIT_STRING_TRIPLE_EDU :

                $indeksSentenceSekarang = 0;                                        //mengacu ke indeks dari array penunjuk jumlah kata per sentence.
                $batasSentenceSekarang = $this->array_jml_kata_per_sentence[0];     //menyimpan batas jumlah kata untuk sentence.

                $totalUnitEdu = count($this->array_jml_kata_per_edu);
                $indexTokenSekarang = 0;
                $indexTokenAkhir = 0;
                $sentenceBaru = true;
                $this->array_unit_string = array();
                $this->array_unit_string_word = array();
                $arrayUnit1 = array();
                $arrayUnit1_word = array();                 //aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                $arrayUnit2 = array();
                $arrayUnit2_word = array();                 //aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                $arrayUnitSebelumnya = array();
                $arrayUnitSebelumnya_word = array();        //aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                $singleEDUSentenceFlag = false; //flag untuk sentence yang hanya mengandung satu EDU.

//Pembantu::cetakDebug("Batas Sentence sekarang : " . $batasSentenceSekarang . "<br/>");
                for($i=0; $i < $totalUnitEdu; $i++)
                {
                    $indexTokenAkhir += $this->array_jml_kata_per_edu[$i];

                    //tentukan batas sentence baru untuk putaran berikutnya.
                    if($indexTokenAkhir == $batasSentenceSekarang && $i >= 0)
                    {
//Pembantu::cetakDebug("Sentence Baru --> $batasSentenceSekarang -- $indexTokenAkhir <br/><br/>");
                        $sentenceBaru = true;
                        $indeksSentenceSekarang++;
                        if($indeksSentenceSekarang < count($this->array_jml_kata_per_sentence))
                        {
                            if($this->array_jml_kata_per_edu[$i] == $this->array_jml_kata_per_sentence[$indeksSentenceSekarang-1])
                            {
                                $singleEDUSentenceFlag = true;
//Pembantu::cetakDebug("SINGLE EDU SENTENCE :: " . $this->array_jml_kata_per_edu[$i] . " VS " . $this->array_jml_kata_per_sentence[$indeksSentenceSekarang-1]);
                            }
                            else
                            {
                                $singleEDUSentenceFlag = false;
                            }

                            $batasSentenceSekarang += $this->array_jml_kata_per_sentence[$indeksSentenceSekarang];
                        }
                    }
                    else
                    {
                        $sentenceBaru = false;
                    }

                    if($i == 0)
                    {
                        $arrayUnit1 = array('NULL');
                        $arrayUnit1_word = array('NULL');//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                    }
                    else
                    {
                        $arrayUnit1 = $arrayUnitSebelumnya;
                        $arrayUnit1_word = $arrayUnitSebelumnya_word;
                    }

                    $arrayUnit2 = array();
                    $arrayUnit2_word = array();//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                    for($j=$indexTokenSekarang;$j<$indexTokenAkhir;$j++)
                    {
                        if((Pembantu::gunakanLemma($this->pilihanKriteria))
                                && (Pembantu::ambilNilaiTag($this->array_pos[$j]) != Umum::FILTER_POS_PRP))     //aboubakr 30-06-2013 : special handling untuk PRP, unit yang dipakai selalu word.
                        {
                            array_push($arrayUnit2, $this->array_token[$j]);        //masukkan token ke array unit string, pada posisi yang sesuai.
                            array_push($arrayUnit2_word, $this->array_word[$j]);    //aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                        }
                        else
                        {
                            array_push($arrayUnit2, $this->array_word[$j]);         //masukkan word ke array unit string, pada posisi yang sesuai.
                            array_push($arrayUnit2_word, $this->array_word[$j]);    //aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                        }
                    }
                    $indexTokenSekarang = $indexTokenAkhir;

                    if(!$arrayUnitSebelumnya && $arrayUnit1[0] != 'NULL')
                    {
//Pembantu::cetakDebug("##############bound1 ".  json_encode($sentenceBaru)." ###############<br/>");
//Pembantu::cetakDebug("Masuk 1.1 NULL<br/>");
//Pembantu::cetakDebug("Masuk 1.2 " . json_encode($arrayUnit2) . "<br/>");
                        array_push($this->array_unit_string, array(array('NULL'),$arrayUnit2));
                        array_push($this->array_unit_string_word, array(array('NULL'),$arrayUnit2_word));//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                    }

                    if($arrayUnit1)
                    {
//Pembantu::cetakDebug("##############bound2 ".  json_encode($sentenceBaru)." ###############<br/>");
//Pembantu::cetakDebug("Masuk 2.1 " . json_encode($arrayUnit1) . "<br/>");
//Pembantu::cetakDebug("Masuk 2.2 " . json_encode($arrayUnit2) . "<br/>");
                        array_push($this->array_unit_string, array($arrayUnit1,$arrayUnit2));
                        array_push($this->array_unit_string_word, array($arrayUnit1_word,$arrayUnit2_word));//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                    }

                    if($sentenceBaru && $i > 0 && ($i < ($totalUnitEdu-1)) && $arrayUnit1)
                    {
//Pembantu::cetakDebug("##############bound3 $batasSentenceSekarang ==== $indexTokenSekarang -- ".  $this->array_jml_kata_per_edu[$i+1] ." ###############<br/>");
//Pembantu::cetakDebug("Masuk 3.1 " . json_encode($arrayUnit2) . "<br/>");
//Pembantu::cetakDebug("Masuk 3.2 NULL <br/>");
                        if(!$singleEDUSentenceFlag) //special handling untuk sentence dengan satu EDU.
                        {
                            array_push($this->array_unit_string, array($arrayUnit2,array('NULL')));
                            array_push($this->array_unit_string_word, array($arrayUnit2_word,array('NULL')));//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                        }
                        $arrayUnitSebelumnya = NULL;
                        $arrayUnitSebelumnya_word = NULL;//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                    }
                    else
                    {
                        if(($arrayUnit1 || !$sentenceBaru) && !($sentenceBaru && $i ==0))   //special handling untuk sentence pertama pada index pertama ---> && !($sentenceBaru && $i ==0)
                        {
                            $arrayUnitSebelumnya = $arrayUnit2;
                            $arrayUnitSebelumnya_word = $arrayUnit2_word;//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                        }
                        else
                        {
                            $arrayUnitSebelumnya = array('NULL');
                            $arrayUnitSebelumnya_word = array('NULL');//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.
                        }
                    }
                }

                if($pilihanUnitString == Umum::UNIT_STRING_TRIPLE_EDU)
                {
//$this->debugMultipleEDU();
//echo "<br/><br/>";
                    $arrayTemp = $this->array_unit_string;
                    $arrayTemp_word = $this->array_unit_string_word;//aboubakr 01102013: untuk hasil akhir (tulis ke file), harus simpan info word.

                    $totalUnitString = count($this->array_unit_string);
                    $this->array_unit_string = array();
                    $this->array_unit_string_word = array();
                    $jumlahSkip = 0;
                    $awalNULL = false;
                    for($i = 0 ; $i < $totalUnitString-1; $i++)
                    {
//Pembantu::cetakDebug("$i --> " . json_encode($arrayTemp[$i][1][0]) . "<br/>");
                        if($arrayTemp[$i][1][0] != 'NULL' && !($awalNULL && $arrayTemp[$i+1][1][0] == 'NULL'))
                        {   //aboubakr 01102013: tambahkan variable2 _word untuk hasil akhir (tulis ke file), harus simpan info word.
                            $this->array_unit_string[$i-$jumlahSkip] = array();
                            $this->array_unit_string_word[$i-$jumlahSkip] = array();
                            array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i][0]);
                            array_push($this->array_unit_string_word[$i-$jumlahSkip], $arrayTemp_word[$i][0]);
//Pembantu::cetakDebug("1 $awalNULL --> " . json_encode($arrayTemp[$i][0]) . "<br/>");
                            array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i][1]);
                            array_push($this->array_unit_string_word[$i-$jumlahSkip], $arrayTemp_word[$i][1]);
//Pembantu::cetakDebug("2 --> " . json_encode($arrayTemp[$i][1]) . "<br/>");
                            if($arrayTemp[$i+1][0][0] == $arrayTemp[$i][1][0])
                            {
                                array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i+1][1]);
                                array_push($this->array_unit_string_word[$i-$jumlahSkip], $arrayTemp_word[$i+1][1]);
//Pembantu::cetakDebug("3.1 --> " . json_encode($arrayTemp[$i+1][1]) . "<br/><br/>");
                            }
                            else
                            {
                                array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i+1][0]);
                                array_push($this->array_unit_string_word[$i-$jumlahSkip], $arrayTemp_word[$i+1][0]);
//Pembantu::cetakDebug("3.2 --> " . json_encode($arrayTemp[$i+1][0]) . "<br/><br/>");
                            }

                            if($arrayTemp[$i][0][0] == 'NULL')
                            {
                                $awalNULL = true;
                            }
                            else
                            {
                                $awalNULL = false;
                            }
                        }
                        else
                        {
                            $jumlahSkip++;
                        }
                    }

                    if($totalUnitString == 1)    // untuk menangani yang total jumlah EDU nya cuma satu.
                    {
                        $this->array_unit_string[0] = array();
                        array_push($this->array_unit_string[0], $arrayTemp[0][0]);
                        array_push($this->array_unit_string[0], $arrayTemp[0][1]);
                        array_push($this->array_unit_string[0], array('NULL'));
                        //aboubakr 01102013: tambahkan variable2 _word untuk hasil akhir (tulis ke file), harus simpan info word.
                        $this->array_unit_string_word[0] = array();
                        array_push($this->array_unit_string_word[0], $arrayTemp_word[0][0]);
                        array_push($this->array_unit_string_word[0], $arrayTemp_word[0][1]);
                        array_push($this->array_unit_string_word[0], array('NULL'));
                    }
//$this->debugMultipleEDU();
//die();
                }
                break;
        }
        
        //abou 21-08-2013 : Memisahkan kembali data POS dan NER sesuai dengan tiap kata dalam unit EDU.. sesuai dengan urutan dalam unit string.

        if($pilihanUnitString == Umum::UNIT_STRING_DOUBLE_EDU || $pilihanUnitString == Umum::UNIT_STRING_TRIPLE_EDU)
        {//aboubakr 01102013: tambahkan variable2 _word untuk hasil akhir (tulis ke file), harus simpan info word.
            $arrayDebugUnitString = array();
            $arrayDebugUnitString_word = array();
            for($i=0;$i<count($this->array_unit_string);$i++)
            {
                for($j=0;$j<count($this->array_unit_string[$i]);$j++)
                {
                    for($k=0;$k<count($this->array_unit_string[$i][$j]);$k++)
                    {
                        if($this->array_unit_string[$i][$j][$k] != "NULL")
                        {
                            $temp_arr = explode("<~>",$this->array_unit_string[$i][$j][$k]);
                            $temp_arr_word = explode("<~>",$this->array_unit_string_word[$i][$j][$k]);
                            array_push($this->array_pos_unit,$temp_arr[1]);
                            array_push($this->array_ner_unit,$temp_arr[2]);
                            $this->array_unit_string[$i][$j][$k] = $temp_arr[0];    //replace with original unit.
                            $this->array_unit_string_word[$i][$j][$k] = $temp_arr_word[0];    //replace with original unit.
                            array_push($arrayDebugUnitString,$temp_arr[0]);
                            array_push($arrayDebugUnitString_word,$temp_arr_word[0]);
                        }
                    }
                }
            }
        }
        else
        {
            for($i=0;$i<count($this->array_unit_string);$i++)
            {
                for($j=0;$j<count($this->array_unit_string[$i]);$j++)
                {
                    $temp_arr = explode("<~>",$this->array_unit_string[$i][$j]);
                    $temp_arr_word = explode("<~>",$this->array_unit_string_word[$i][$j]);
                    array_push($this->array_pos_unit,$temp_arr[1]);
                    array_push($this->array_ner_unit,$temp_arr[2]);
                    $this->array_unit_string[$i][$j] = $temp_arr[0];    //replace with original unit.
                    $this->array_unit_string_word[$i][$j] = $temp_arr_word[0];    //replace with original unit.
                }
            }
        }

//Pembantu::cetakDebug("<span style='color:red;'>ARRAY WORD : </span>" . json_encode($this->array_word) . "<br/>");
//Pembantu::cetakDebug("<span style='color:red;'>ARRAY TOKEN : </span>" . json_encode($this->array_token) . "<br/>");
//Pembantu::cetakDebug("<span style='color:red;'>ARRAY UNIT STRING : </span>" . json_encode($this->array_unit_string) . "<br/>");
//
//Pembantu::cetakDebug("<span style='color:red;'>ARRAY UNIT STRING DEBUG (" . count($arrayDebugUnitString) . ") : </span>" . json_encode($arrayDebugUnitString) . "<br/>");
//Pembantu::cetakDebug("<span style='color:red;'>ARRAY NER UNIT (" . count($this->array_ner_unit) . ") : </span>" . json_encode($this->array_ner_unit) . "<br/>");
//Pembantu::cetakDebug("<span style='color:red;'>ARRAY POS UNIT (" . count($this->array_pos_unit) . ") : </span>" . json_encode($this->array_pos_unit) . "<br/>");
        
        //Kembalikan array_word dan array_token ke nilai awal...
        $this->array_word = array();
        $this->array_word = $this->array_word_ori;
        $this->array_word_ori = array();

        $this->array_token = array();
        $this->array_token = $this->array_token_ori;
        $this->array_token_ori = array();
    }

    private function debugMultipleEDU($namaFile)
    {
        $string_hasil = "";
        $total1 = count($this->array_unit_string_word);
        for($i=0;$i<$total1;$i++)
        {
            $total2 = count($this->array_unit_string_word[$i]);
            for($j=0;$j<$total2;$j++)
            {
                $total3 = count($this->array_unit_string_word[$i][$j]);
                for($k=0;$k<$total3;$k++)
                {
                    if(!($j%2))
                    {
                        $color = 'black';
                    }
                    else
                    {
                        $color = 'blue';
                    }
                    echo "<span style='color:$color;'>" . $this->array_unit_string_word[$i][$j][$k] . " </span>";
                    $string_hasil .= $this->array_unit_string_word[$i][$j][$k] . " ";
                }
                if($j < $total2-1)
                {
                    echo "<span style='color:red;'> ### </span>";
                    $string_hasil .= " ### ";
                }
            }
            $string_hasil .= "\n";
            echo "<br/>";
            echo "<br/>";

            $fptr = fopen($namaFile,"w");
            fwrite($fptr, $string_hasil);
            fclose($fptr);
        }
    }

}
?>
