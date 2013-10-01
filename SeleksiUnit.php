<?php
include_once 'Unit.php';
include_once 'TulisHasil.php';
include_once 'Pembantu.php';

class SeleksiUnit {

    private $array_nama_file_masukan;       // array yang menyimpan list nama entitas file news dalam satu folder input.
    private $array_objek_unit;              // array yang menyimpan objek unit.
    private $folderMasukan;                 // folder tempat file-file masukan
    private $penanda_filter_token_entity;   // penanda filter token atau entity (NER), nilai-nilai penanda sesuai urutan, mulai dari 0 :: TANPA_KRITERIA, LEMMA, NN (semua POS noun), VB (semua POS verb), JJ, PRP, PERSON, LOCATION, ORGANIZATION, DATE, MONEY, TIME, NUMBER, MISC.
    private $penanda_unit_string;           // penanda unit string, nilai-nilai penanda sesuai urutan, mulai dari 0 :: DOKUMEN, SENTENCE, SINGLE_EDU, DOUBLE_EDU, TRIPLE_EDU.
    private $array_stopwords;               // array stopwords, diambil dari file stopwords.
    private $array_index_alph_stopwords;    // array index dari alphabet. menggunakan array associative. mulai 'a' sampai 'z' jika terdapat pada daftar kata stopwords.
    private $perluFilterStopWords;          // penanda perlu atau tidaknya filter terhadap stopwords, jika tidak perlu, skip semua pemrosesan terhadap stopwords.

    private $array_hasil_filtering;         // array yang menyimpan token atau entity hasil filtering.
    private $array_freq_hasil_filtering;    // array yang menyimpan tabel frekuensi dari perhitungan kemunculan token2 hasil filtering.

    function __construct($penandaFilterTokenEntity,$penandaUnitString,$folderMasukan=Umum::FOLDER_MASUKAN_SELEKSI_UNIT) {
        $this->array_nama_file_masukan      = array();
        $this->array_objek_unit             = array();
        $this->folderMasukan                = $folderMasukan;
        $this->penanda_filter_token_entity  = $penandaFilterTokenEntity;    // berisi nilai untuk masking bit filter. pencocokan menggunakan masking lebih cepat, tidak banyak looping.
        $this->penanda_unit_string          = $penandaUnitString;
        $this->array_hasil_filtering        = array();
        $this->array_freq_hasil_filtering   = array();

        //if( !Pembantu::gunakanLemma($this->penanda_filter_token_entity) || ($this->penanda_filter_token_entity & Umum::FILTER_TANPA_KRITERIA) ) // cek apakah filter terhadap stopWords diperlukan. Hanya digunakan selain NER.

//if( Pembantu::gunakanLemma($this->penanda_filter_token_entity) )
//{
//    echo "[aboubakrTmp]Gunakan Lemma... " . $this->penanda_filter_token_entity . "<br/>";
//}
        if( !Pembantu::gunakanLemma($this->penanda_filter_token_entity) || !($this->penanda_filter_token_entity & Umum::FILTER_TANPA_KRITERIA) ) // aboubakr : 20120903 -> mengubah kondisi filter tanpa kriteria.
        {
//echo "[aboubakrTmp] Filter StopWords " . $this->penanda_filter_token_entity . "<br/>";
            $this->perluFilterStopWords = true;
        }
        else
        {
//echo "[aboubakrTmp] TIDAK Filter StopWords " . $this->penanda_filter_token_entity . "<br/>";
            $this->perluFilterStopWords = false;
        }

        if($this->perluFilterStopWords) // skip filter stopWords jika tidak diperlukan.
        {
            $this->array_stopwords = array();
            $this->array_index_alph_stopwords = array();
            $this->persiapkanDaftarStopwords();
        }
        $this->muatNamaFileMasukan();
        $this->muatObjekFileMasukan();
    }

    //private function muatNamaFileMasukan() :: memuat nama file masukan.
    private function muatNamaFileMasukan()
    {
        if(!is_dir($this->folderMasukan))
        {
            echo "Data tidak lengkap.. Persiapkan file2 hasil konversi XML... Simpan dalam ". $this->folderMasukan ."<br/>";
            exit(0);
        }

        $handle = opendir($this->folderMasukan);
        $this->array_nama_file_masukan = array();

        if($handle)
        {
            $i = 0;
            while(false !== ($file = readdir($handle)))
            {
                if($file != "." && $file != ".." && !strcasecmp(substr($file, strlen($file) - strlen(Umum::AKHIRAN_NERPOS_FILE)), Umum::AKHIRAN_NERPOS_FILE))
                {
                    array_push($this->array_nama_file_masukan, substr($file, 0, 0-strlen(Umum::AKHIRAN_NERPOS_FILE)) );    // yang disimpan hanya bagian nama utamanya saja, hilangkan akhirannya.
                    $i++;
                }
            }
            closedir($handle);
        }
    }

    //private function muatObjekFileMasukan() :: buat semua objek unit dari semua file yang ada di Folder Masukan.
    private function muatObjekFileMasukan()
    {
echo "<br/><br/>**********MEMUAT UNIT STRING SESUAI UNIT STRING PILIHAN*********<br/><br/>";
        for($i=0; $i < count($this->array_nama_file_masukan); $i++)
        {
            $objek = new Unit($this->array_nama_file_masukan[$i], $this->folderMasukan,$this->penanda_unit_string,$this->penanda_filter_token_entity);
            array_push($this->array_objek_unit, $objek);
        }
    }

    //private function persiapkanDaftarStopwords()
    private function persiapkanDaftarStopwords()
    {
        $CC = '-';
        $idx = 0;
        $fptr = fopen(Umum::ALAMAT_FILE_STOPWORDS, "r");
        $buff = "";

        if(!$fptr)
        {
            echo "File " . Umum::ALAMAT_FILE_STOPWORDS . " tidak ditemukan... Silahkan dilengkapi...<br/>";
            die();
        }

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

    //private function filterArrayToken() :: menyaring array masukan, menghasilkan array token hasil filtering.
    private function filterArrayToken($objekUnit)
    {
        /* @var $objekUnit Unit */
        $arrayHasil = array();
        $totalToken = count($objekUnit->array_word);
//echo "ITEM WORD :: " . json_encode($objekUnit->array_word) . "<br/>";
        for($i=0; $i < $totalToken; $i++)
        {
            $POS = $objekUnit->array_pos[$i];
            if(Pembantu::ambilNilaiTag($POS) == Umum::FILTER_POS_PRP)       //aboubakr 30-06-2013 : special handling untuk PRP, unit yang dipakai selalu word.
            {
                $kataTarget = $objekUnit->array_word[$i];      // gunakan word untuk PRP.
            }
            else
            {
                $kataTarget = $objekUnit->array_token[$i];      // gunakan lemma.
            }

            $perluProses = true;

            // 20120906 :: untuk memastikan apabila pilihan kategori adalah dalam pilihan POS (saja), maka berlaku proses filtering, tag POS harus sama. Apabila pilihan kategori adalah lemma, maka semua lemma akan lolos tanpa dicek POS nya. ;)
            if(!(Pembantu::ambilNilaiTag($POS) & $this->penanda_filter_token_entity) && ($this->penanda_filter_token_entity > Umum::FILTER_LEMMA))
            {
                $perluProses = false;
            }

            if(!$perluProses)   // skip jika tidak perlu diproses.
            {
                continue;
            }

            if(Pembantu::masukkanElemenUnikTerurut($arrayHasil,$kataTarget, $this->array_stopwords,$this->array_index_alph_stopwords) !== false)
            {
                //Pembantu::cetakDebug("elemen ke-" . count($arrayHasil) . " $kataTarget => $POS <br/>");
            }
            else
            {
                //Pembantu::cetakDebug("Kata termasuk dalam stopwords :: $kataTarget <br/>");
            }
        }
//echo "<br/><br/>";
        return $arrayHasil;
    }

    //private function filterArrayEntity() :: menyaring array masukan, menghasilkan array entity (NER) hasil filtering.
    private function filterArrayEntity($objekUnit)
    {
        /* @var $objekUnit Unit */

        $arrayHasil = array();
        $totalToken = count($objekUnit->array_word);
        $indexSebelumnya = -999;        // digunakan untuk pengecekan, apakah entity terdiri dari lebih dari satu kata. jika berurutan dengan kata sebelumnya, berarti terdiri lebih dari satu kata.
        $indexElemenSebelumnya = 0;     // indeks elemen sebelumnya di array hasil. (digunakan untuk memasukkan entity)

        //26-06-2013: tambahan 2 variable di parameter Pembantu::masukkanElemenUnikTerurut, untuk menangani problem missing entity.
        $kataSamaSebelumnya = null;
        for($i=0; $i < $totalToken; $i++)
        {
            $NER = $objekUnit->array_ner[$i];
            $kataTarget = $objekUnit->array_word[$i];      // gunakan kata.

            $perluProses = true;

            if(!(Pembantu::ambilNilaiTag($NER) & $this->penanda_filter_token_entity) && !(Umum::FILTER_TANPA_KRITERIA & $this->penanda_filter_token_entity))
            {
                $perluProses = false;
            }

            if(!$perluProses)   // skip jika tidak perlu diproses.
            {
//echo "TIDAK PERLU PROSES ENTITY<br/>";
                continue;
            }

            if(($i - $indexSebelumnya == 1) && !(Umum::FILTER_TANPA_KRITERIA & $this->penanda_filter_token_entity) //jika berurutan.. gabungkan ke elemen entity sebelumnya.
                    && ($objekUnit->array_ner[$i] == $objekUnit->array_ner[$indexSebelumnya]))  //aboubakr 01102013 : tambahkan kondisi, NER harus sama dengan yang sebelumnya, baru digabungkan. Memperbaiki error NUMBER yang tergabung ke LOCATION, dll.
            {
//echo "GABUNG to $indexElemenSebelumnya : $kataTarget , POS: $POSTarget , NER: $NERTarget<br/>";
                //20130401 : menemukan problem disini, menyebabkan double token, solusi sementara, selalu cek terhadap elemen-elemen sebelumnya.
                //20130626 : menemukan problem lain, substring yang tidak dimasukkan ke arrayHasil karena kata awalnya sudah ada di array sebelumnya. contoh: "Kennedy" dengan "Kennedy Administration" -> "Kennedy Administration" tidak dimasukkan ke arrayHasil. Solusi : beri flag ketika terjadi kejadian serupa.
                If($kataSamaSebelumnya != null) //disini terjadi kasus di atas, flag diset.
                {
                    array_push($arrayHasil,$kataSamaSebelumnya . " " . $kataTarget);
                    $kataSamaSebelumnya = null;
                    $indexElemenSebelumnya = count($arrayHasil)-1;
                }
                else
                {
                    $arrayHasil[$indexElemenSebelumnya] = $arrayHasil[$indexElemenSebelumnya] . " " . $kataTarget;
                }
            }
            else
            {
//echo "PISAH $kataTarget , POS: $POSTarget , NER: $NERTarget<br/>";
                if(Umum::FILTER_TANPA_KRITERIA & $this->penanda_filter_token_entity)    //20120907 : jika pilihan kategori adalah TANPA_KRITERIA, maka tetap gunakan filtering stopwords.
                {
                    $indexElemenSebelumnya = Pembantu::masukkanElemenUnikTerurut($arrayHasil,$kataTarget, $this->array_stopwords,$this->array_index_alph_stopwords,false,true,$kataSamaSebelumnya);
                }
                else
                {
//Pembantu::cetakDebug("debug2 : $kataTarget in " . json_encode($arrayHasil) . "<br/>");
                    $indexElemenSebelumnya = Pembantu::masukkanElemenUnikTerurut($arrayHasil,$kataTarget, $this->array_stopwords,$this->array_index_alph_stopwords,true,true,$kataSamaSebelumnya);
                }
            }
            $indexSebelumnya = $i;  //simpan index sebelumnya
        }        
        return array_merge(array_flip(array_flip($arrayHasil))); //20130401 : see http://www.php.net/manual/en/function.array-unique.php#70786
    }

    //public function pemrosesanSeleksiUnit() :: lakukan filtering berdasarkan pilihan kriteria.
    public function pemrosesanSeleksiUnit()
    {
echo("<br/><br/>**********HASIL FILTERING TOKEN / ENTITY*********<br/><br/>");
        //pemfilteran berdasarkan pilihan kriteria.
        if(Pembantu::gunakanLemma($this->penanda_filter_token_entity))  // gunakan pemfilteran token.
        {
Pembantu::cetakDebug("Gunakan Filter Token<br/>");
            $totalUnit = count($this->array_objek_unit);
            for($i=0; $i < $totalUnit; $i++)
            {
                $this->array_hasil_filtering[$i] = $this->filterArrayToken($this->array_objek_unit[$i]);
echo("Hasil untuk file " . $this->array_objek_unit[$i]->nama . " :: " . json_encode($this->array_hasil_filtering[$i]) . "<br/><br/>");
            }
        }
        else  // gunakan pemfilteran entity.
        {
Pembantu::cetakDebug("Gunakan Filter Entity<br/>");
            $totalUnit = count($this->array_objek_unit);
            for($i=0; $i < $totalUnit; $i++)
            {
                $this->array_hasil_filtering[$i] = $this->filterArrayEntity($this->array_objek_unit[$i]);
echo("Hasil untuk file " . $this->array_objek_unit[$i]->nama . " :: " . json_encode($this->array_hasil_filtering[$i]) . "<br/><br/>");
            }
        }

echo "<br/><br/>**********HASIL PERHITUNGAN FREKUENSI KEMUNCULAN TOKEN PADA TIAP UNIT STRING*********<br/><br/>";
        //Hitung Frekuensi kemunculan token berdasarkan string unit.
        $totalHasil = count($this->array_hasil_filtering);
        $this->array_freq_hasil_filtering = array();
        $isMultipleEDU = false;
        $jumlahUnitString = count($this->array_objek_unit[0]->array_unit_string[0]);    //menentukan apakah double EDU atau triple EDU.

        if($this->penanda_unit_string == Umum::UNIT_STRING_DOUBLE_EDU || $this->penanda_unit_string == Umum::UNIT_STRING_TRIPLE_EDU)  // jika unit string adalah double EDU atau Triple EDU
        {
            $isMultipleEDU = true;
        }

        for($i=0; $i<$totalHasil;$i++)
        {
            $totalUnitString = count($this->array_objek_unit[$i]->array_unit_string);   // jumlah unit string.
            $this->array_freq_hasil_filtering[$i] = array();
            $awalIdxNER = 0;

            //abou 25-08-2013 : unused
//            $idxAwalUnitSebelumnya = 0;
//            $jumlahKataUnitSebelumnya = 0;
//            $jumlahKataUnitSebelumnya_akhir = 0;    //khusus triple EDU

echo("Total UnitString = $totalUnitString<br/>");
            for($j=0;$j<$totalUnitString;$j++)
            {
                $this->array_freq_hasil_filtering[$i][$j] = array();
                $jumlahToken = count($this->array_hasil_filtering[$i]);
                $indexAkhirPencarian = count($this->array_objek_unit[$i]->array_unit_string[$j]);

                /*
                 * abou 25-08-2013 : Tidak perlu digunakan, NER dan POS sudah tersimpan sesuai urutan per kata dalam array unit string.
                 * 
                if($isMultipleEDU)      //aboubakr 20120916 : tentukan awalIdxkata tiap unitnya
                {
                    if($this->array_objek_unit[$i]->array_unit_string[$j][0][0] != 'NULL')
                    {
                        $awalIdxNER = $idxAwalUnitSebelumnya;
                    }
                    else
                    {
                        $awalIdxNER = $idxAwalUnitSebelumnya + $jumlahKataUnitSebelumnya;
                    }

                    //aboubakr 20120916 : Hitung jumlah kata Unit sebelumnya
                    // jika akhirnya tidak NULL
                    if($this->array_objek_unit[$i]->array_unit_string[$j][$jumlahUnitString-1][0] != 'NULL')
                    {
                        $jumlahKataUnitSebelumnya_akhir = count($this->array_objek_unit[$i]->array_unit_string[$j][$jumlahUnitString-1]);

                        $idxAwalUnitSebelumnya += $jumlahKataUnitSebelumnya;
                        if($this->penanda_unit_string == Umum::UNIT_STRING_TRIPLE_EDU)  // aboubakr 20120916 : tambahan untuk triple EDU.
                        {
                            $jumlahKataUnitSebelumnya = count($this->array_objek_unit[$i]->array_unit_string[$j][$jumlahUnitString-2]);
                            $jumlahKataUnitSebelumnya += count($this->array_objek_unit[$i]->array_unit_string[$j][$jumlahUnitString-1]);
                        }
                        else
                        {
                            $jumlahKataUnitSebelumnya = count($this->array_objek_unit[$i]->array_unit_string[$j][$jumlahUnitString-1]);  // aboubakr 20120916 : hitung jumlah kata unit terakhir, karena tidak null.
                        }
                    }
                    else    // unit terakhir NULL.
                    {
                        $jumlahKataUnitSebelumnya = count($this->array_objek_unit[$i]->array_unit_string[$j][0]);  // aboubakr 20120916 : hitung jumlah kata unit awal, karena unit terakhir null. Dipastikan ada.
                        if($this->penanda_unit_string == Umum::UNIT_STRING_TRIPLE_EDU)  // aboubakr 20120916 : tambahan untuk triple EDU.
                        {
                            $jumlahKataUnitSebelumnya += count($this->array_objek_unit[$i]->array_unit_string[$j][1]);  // aboubakr 20120916 : tambahkan untuk triple edu.
                        }
                    }
//echo "Unit ke-$j -> " . $this->array_objek_unit[$i]->array_unit_string[$j][0][0] . " :: idxSebelum = $idxAwalUnitSebelumnya , jumlahKataSebelum = $jumlahKataUnitSebelumnya , awalIndex = $awalIdxNER<br/>";
                }
                */
                
//Pembantu::cetakDebug("[abouDebug] NER Array " . json_encode($this->array_objek_unit[$i]->array_ner) . "<br/>");//suspect 18022013 : ga dicek di fungsi hitungKata!!!
                
                $arrayUnitStringMultiEDU = array(); //abou 25-08-2013 : gunakan sebagai iterator awalIdxNER.
                for($k = 0; $k < $jumlahToken; $k++)
                {
                    if($isMultipleEDU)  // jika unit string adalah double EDU atau Triple EDU
                    {
                        if($k==0)   //abou 25-08-2013 : cukup sekali proses, gunakan untuk sisa token.
                        {
                            $totalKataDalamUnit = 0;
                            for($x=0;$x<$jumlahUnitString;$x++)
                            {
                                $jumlahTokenUnitString = count($this->array_objek_unit[$i]->array_unit_string[$j][$x]);

                                for($y=0;$y<$jumlahTokenUnitString;$y++)
                                {
                                    $totalKataDalamUnit++;
                                    if($this->array_objek_unit[$i]->array_unit_string[$j][$x][$y] != 'NULL')
                                    {
                                        array_push($arrayUnitStringMultiEDU, $this->array_objek_unit[$i]->array_unit_string[$j][$x][$y]);
                                    }
                                }
                            }
                        }

//Pembantu::cetakDebug("Kata Dicari : " . $this->array_hasil_filtering[$i][$k] . " ,  array target cari sampai index ke-$indexAkhirPencarian : " . json_encode($arrayUnitStringMultiEDU) . "<br/>");                        
                        //16062013 : Pisahkan untuk pengecekan NER dan POS. Jika Filter yang digunakan filter NER, supply array NER untuk parameter ke 3, otherwise, supply array POS.
                        if($this->penanda_filter_token_entity > Umum::FILTER_MASK_POS)
                        {
                            //$this->array_freq_hasil_filtering[$i][$j][$k] = Pembantu::hitungKata($this->array_hasil_filtering[$i][$k],$arrayUnitStringMultiEDU,$this->array_objek_unit[$i]->array_ner,$awalIdxNER,$this->penanda_filter_token_entity,0,$totalKataDalamUnit-1,true);
                            $this->array_freq_hasil_filtering[$i][$j][$k] = Pembantu::hitungKata($this->array_hasil_filtering[$i][$k],$arrayUnitStringMultiEDU,$this->array_objek_unit[$i]->array_ner_unit,$awalIdxNER,$this->penanda_filter_token_entity,0,$totalKataDalamUnit-1,true);
                        }
                        else
                        {
                            //$this->array_freq_hasil_filtering[$i][$j][$k] = Pembantu::hitungKata($this->array_hasil_filtering[$i][$k],$arrayUnitStringMultiEDU,$this->array_objek_unit[$i]->array_pos,$awalIdxNER,$this->penanda_filter_token_entity,0,$totalKataDalamUnit-1,true);
                            $this->array_freq_hasil_filtering[$i][$j][$k] = Pembantu::hitungKata($this->array_hasil_filtering[$i][$k],$arrayUnitStringMultiEDU,$this->array_objek_unit[$i]->array_pos_unit,$awalIdxNER,$this->penanda_filter_token_entity,0,$totalKataDalamUnit-1,true);
                        }
                    }
                    else    // untuk selain multiple EDU.
                    {
//Pembantu::cetakDebug("Kata Dicari : " . $this->array_hasil_filtering[$i][$k] . "  array target cari sampai index ke-$indexAkhirPencarian <br/>");
                        if($this->penanda_filter_token_entity > Umum::FILTER_MASK_POS)
                        {
                            $this->array_freq_hasil_filtering[$i][$j][$k] = Pembantu::hitungKata($this->array_hasil_filtering[$i][$k],$this->array_objek_unit[$i]->array_unit_string[$j],$this->array_objek_unit[$i]->array_ner,$awalIdxNER,$this->penanda_filter_token_entity,0,$indexAkhirPencarian,false);
                        }
                        else
                        {
                            $this->array_freq_hasil_filtering[$i][$j][$k] = Pembantu::hitungKata($this->array_hasil_filtering[$i][$k],$this->array_objek_unit[$i]->array_unit_string[$j],$this->array_objek_unit[$i]->array_pos,$awalIdxNER,$this->penanda_filter_token_entity,0,$indexAkhirPencarian,false);
                        }
                    }
                }

                if(!$isMultipleEDU) //aboubakr 20120916 : jika bukan multiple EDU.
                {
                    $awalIdxNER += $indexAkhirPencarian;
                }
                else
                {
                    $awalIdxNER += count($arrayUnitStringMultiEDU);
                }
            }
Pembantu::cetakDebug("Hasil Frekuensi Untuk File - " . $this->array_objek_unit[$i]->nama . " ==> " . json_encode($this->array_freq_hasil_filtering[$i]) . "<br/><br/>");
        }

// Tulis hasil modul seleksi unit.
        $akhiranNamaFile = "";
        if($this->penanda_unit_string == Umum::UNIT_STRING_DOKUMEN)
        {
            $akhiranNamaFile = Umum::AKHIRAN_TABEL_FREQ_DOC;
        }
        else if($this->penanda_unit_string == Umum::UNIT_STRING_SENTENCE)
        {
            $akhiranNamaFile = Umum::AKHIRAN_TABEL_FREQ_STC;
        }
        else
        {
            $akhiranNamaFile = Umum::AKHIRAN_TABEL_FREQ_EDU;
        }

        if(!is_dir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_TOKEN))
        {
            mkdir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_TOKEN,0777,true);        //buat folder keluaran 1.            
        }
        if(!is_dir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_FREQ_TABLE))
        {
            mkdir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_FREQ_TABLE,0777,true);   //buat folder keluaran 2.
        }
//        mkdir(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_FREQ_TABLE_TR,0,true); //buat folder keluaran 2 - transpose. No Need
        for($i=0;$i<$totalHasil;$i++)
        {
            TulisHasil::tuliskanHasilSeleksiUnit(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_TOKEN . $this->array_objek_unit[$i]->nama . Umum::AKHIRAN_HASIL_SELEKSI_UNIT,$this->array_hasil_filtering[$i],false);
            TulisHasil::tuliskanTabelFrekuensiSeleksiUnit(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_FREQ_TABLE . $this->array_objek_unit[$i]->nama . $akhiranNamaFile, $this->array_freq_hasil_filtering[$i],false);
//            TulisHasil::tuliskanTabelFrekuensiSeleksiUnit(Umum::FOLDER_HASIL_SELEKSI_UNIT . $_SESSION["timestamp"] . Umum::FOLDER_FILTERED_FREQ_TABLE_TR . $this->array_objek_unit[$i]->nama . $akhiranNamaFile, $this->array_freq_hasil_filtering[$i],true);
        }
    }

}
?>
