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
    public $array_token;                    // array of token (lemma), diambil dari file NERPOS, index ke 3 dari tabel NERPOS.
    public $array_pos;                      // array of POS, diambil dari file NERPOS, index ke 6 dari tabel NERPOS.
    public $array_ner;                      // array of NER, diambil dari file NERPOS, index ke 7 dari tabel NERPOS.
    public $array_unit_string;              // array unit String setelah diproses.
    public $pilihanKriteria;                // pilihan kriteria.

    /*****************/
    /* FUNGSI-FUNGSI */
    /*****************/
    public function __construct($nama,$folder,$pilihanUnitString=false,$pilihanKriteria=false) {
        $this->nama                         = $nama;
        $this->array_jml_kata_per_edu       = array();
        $this->array_jml_kata_per_sentence  = array();
        $this->array_word                   = array();
        $this->array_token                  = array();
        $this->array_pos                    = array();
        $this->array_ner                    = array();
        $this->pilihanKriteria              = $pilihanKriteria;

        $this->persiapkanFileUnit($folder);
        if($pilihanUnitString !== false)      // jika bernilai false (nilai default), maka tidak dilakukan pemrosesan berdasar unit string pilihan. Jika tidak bernilai false, maka unit akan diproses berdasar unit string yang dipilih dan hasiln disimpan pada array $array_unit_string.
        {
            $this->array_unit_string = array();
            $this->persiapkanUnitString($pilihanUnitString);
//echo "Unit string untuk file - $this->nama :: " . json_encode($this->array_unit_string) . "<br/>";
echo "<span style='font-weight:bold;color:brown;font-size:large;'>=========Total untuk file $nama : " . count($this->array_unit_string) . " Unit.=========</span><br/><br/>";
            if($pilihanUnitString >= Umum::UNIT_STRING_DOUBLE_EDU)
            {
                mkdir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_LIST_MULTIPLE_UNIT,0777,true);    //buat folder keluaran 3.   //aboubakr 27012013 : tambah file keluaran, list unit untuk multiple EDU.
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
        Pembantu::muatFileNERPOS($folder . $this->nama . Umum::AKHIRAN_NERPOS_FILE,$this->array_word,$this->array_token,$this->array_pos,$this->array_ner);
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
                    $this->array_unit_string[0] = &$this->array_token;
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
                        if(Pembantu::gunakanLemma($this->pilihanKriteria))
                        {
                            array_push($this->array_unit_string[$i], $this->array_token[$j]);    //masukkan token ke array unit string, pada posisi yang sesuai.
                        }
                        else
                        {
                            array_push($this->array_unit_string[$i], $this->array_word[$j]);    //masukkan token ke array unit string, pada posisi yang sesuai.
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
                        if(Pembantu::gunakanLemma($this->pilihanKriteria))
                        {
                            array_push($this->array_unit_string[$i], $this->array_token[$j]);    //masukkan token ke array unit string, pada posisi yang sesuai.
                        }
                        else
                        {
                            array_push($this->array_unit_string[$i], $this->array_word[$j]);    //masukkan token ke array unit string, pada posisi yang sesuai.
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
                $arrayUnit1 = array();
                $arrayUnit2 = array();
                $arrayUnitSebelumnya = array();

                for($i=0; $i < $totalUnitEdu; $i++)
                {
                    $indexTokenAkhir += $this->array_jml_kata_per_edu[$i];

                    //tentukan batas sentence baru untuk putaran berikutnya.
                    if($indexTokenAkhir == $batasSentenceSekarang && $i > 0)
                    {
//echo "Sentence Baru --> $batasSentenceSekarang -- $indexTokenAkhir <br/><br/>";
                        $sentenceBaru = true;
                        $indeksSentenceSekarang++;
                        if($indeksSentenceSekarang < count($this->array_jml_kata_per_sentence))
                        {
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
                    }
                    else
                    {
                        $arrayUnit1 = $arrayUnitSebelumnya;
                    }

                    $arrayUnit2 = array();
                    for($j=$indexTokenSekarang;$j<$indexTokenAkhir;$j++)
                    {
                        if(Pembantu::gunakanLemma($this->pilihanKriteria))
                        {
                            array_push($arrayUnit2, $this->array_token[$j]);    //masukkan token ke array unit string, pada posisi yang sesuai.
                        }
                        else
                        {
                            array_push($arrayUnit2, $this->array_word[$j]);    //masukkan token ke array unit string, pada posisi yang sesuai.
                        }
                    }
                    $indexTokenSekarang = $indexTokenAkhir;

                    if(!$arrayUnitSebelumnya && $arrayUnit1[0] != 'NULL')
                    {
                        array_push($this->array_unit_string, array(array('NULL'),$arrayUnit2));
                    }

                    if($arrayUnit1)
                    {
                        array_push($this->array_unit_string, array($arrayUnit1,$arrayUnit2));
                    }

                    if($sentenceBaru && $i > 0 && $arrayUnit1)
                    {
//echo "Masuk " . json_encode($arrayUnit1) . "<br/>";
                        array_push($this->array_unit_string, array($arrayUnit2,array('NULL')));
                        $arrayUnitSebelumnya = NULL;
                    }
                    else
                    {
                        if($arrayUnit1 || !($sentenceBaru && $i > 0))
                        {
                            $arrayUnitSebelumnya = $arrayUnit2;
                        }
                        else
                        {
                            $arrayUnitSebelumnya = array('NULL');
                        }
                    }
                }

                if($pilihanUnitString == Umum::UNIT_STRING_TRIPLE_EDU)
                {
//$this->debugMultipleEDU();
//echo "<br/><br/>";
                    $arrayTemp = $this->array_unit_string;
                    $totalUnitString = count($this->array_unit_string);
                    $this->array_unit_string = array();
                    $jumlahSkip = 0;
                    $awalNULL = false;
                    for($i = 0 ; $i < $totalUnitString-1; $i++)
                    {
//echo "$i --> " . json_encode($arrayTemp[$i][1][0]) . "<br/>";
                        if($arrayTemp[$i][1][0] != 'NULL' && !($awalNULL && $arrayTemp[$i+1][1][0] == 'NULL'))
                        {
                            $this->array_unit_string[$i-$jumlahSkip] = array();
                            array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i][0]);
//echo "1 $awalNULL --> " . json_encode($arrayTemp[$i][0]) . "<br/>";
                            array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i][1]);
//echo "2 --> " . json_encode($arrayTemp[$i][1]) . "<br/>";
                            if($arrayTemp[$i+1][0][0] == $arrayTemp[$i][1][0])
                            {
                                array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i+1][1]);
//echo "3 --> " . json_encode($arrayTemp[$i+1][1]) . "<br/><br/>";
                            }
                            else
                            {
                                array_push($this->array_unit_string[$i-$jumlahSkip], $arrayTemp[$i+1][0]);
//echo "3 --> " . json_encode($arrayTemp[$i+1][0]) . "<br/><br/>";
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
                    }
//$this->debugMultipleEDU();
//die();
                }
                break;
        }
    }

    private function debugMultipleEDU($namaFile)
    {
        $string_hasil = "";
        $total1 = count($this->array_unit_string);
        for($i=0;$i<$total1;$i++)
        {
            $total2 = count($this->array_unit_string[$i]);
            for($j=0;$j<$total2;$j++)
            {
                $total3 = count($this->array_unit_string[$i][$j]);
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
                    echo "<span style='color:$color;'>" . $this->array_unit_string[$i][$j][$k] . " </span>";
                    $string_hasil .= $this->array_unit_string[$i][$j][$k] . " ";
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
