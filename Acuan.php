<?php
include_once 'Umum.php';
include_once 'Pembantu.php';
/**
 * Acuan adalah suatu class yang menunjukkan entitas file acuan, yang digunakan sebagai pembanding.
 *
 * @author Abu Bakar Gadi
 */

class Acuan {

    /*************/
    /* ATTRIBUTE */
    /*************/
    public  $nama;
    public  $array_jml_kata_per_edu;        // array jumlah kata untuk tiap EDU, sesuai urutan EDU.
    public  $array_jml_kata_per_sentence;   // array jumlah kata untuk tiap sentence, sesuai urutan sentence.
    public  $array_stopwords;               // array stopwords, diambil dari file stopwords.
    private $array_index_alph_stopwords;    // array index dari alphabet. menggunakan array associative. mulai 'a' sampai 'z' jika terdapat pada daftar kata stopwords.
    public  $array_token;                   // array token, diambil dari file NERPOS, index ke 3 dari tabel NERPOS.
    public  $array_token_non_stopwords;     // array token yang telah dibersihkan dari stopwords.
    public  $array_token_bersih;            // array token yang telah dibersihkan dari stopwords dan telah dibuat unique.
    public  $array_ner;                     // array nerpos, diambil dari file NERPOS, index ke 6 dari tabel NERPOS.
    public  $array_pos;                     // array nerpos, diambil dari file NERPOS, index ke 6 dari tabel NERPOS.
    public  $array_word;                    // array word, diambil dari file NERPOS, index ke 2 dari tabel NERPOS.

    /*****************/
    /* FUNGSI-FUNGSI */
    /*****************/
    public function __construct($nama,$array_stopwords,$array_index_alphabet_stopwords)
    {
        $this->nama = $nama;
        $this->array_stopwords              = $array_stopwords;
        $this->array_index_alph_stopwords   = $array_index_alphabet_stopwords;
        $this->array_token                  = array();
        $this->array_token_bersih           = array();
        $this->array_token_non_stopwords    = array();
        $this->array_word                   = array();
        $this->array_ner                    = array();
        $this->array_pos                    = array();
        $this->array_jml_kata_per_edu       = array();
        $this->array_jml_kata_per_sentence  = array();

//        $this->persiapkanDaftarStopwords(); // memuat daftar stopword kedalam array_stopwords dan menyimpan index awal tiap kelompok kata berdasarkan alphabetnya.
        $this->persiapkanFileAcuan();       // mempersiapkan file acuan, bersihkan dari stopwords dan buat menjadi unique tiap elemennya.
    }

    public function persiapkanFileAcuan()
    {
//echo '[abouDebug]....Mempersiapkan File Acuan....<br/>';
        //Mengambil token dan nerpos.
        Pembantu::muatFileNERPOS(false,Umum::FOLDER_ACUAN . $this->nama . Umum::AKHIRAN_NERPOS_FILE,$this->array_word,$this->array_token,$this->array_ner,$this->array_pos,null,null);  //abou 21-08-2013 : sesuaikan dengan modifikasi fungsi untuk load NERPOS.
        //Ambil jumlah kata untuk tiap unit
        Pembantu::muatFileEDU(Umum::FOLDER_ACUAN . $this->nama . Umum::AKHIRAN_EDU_FILE, $this->array_jml_kata_per_edu, $this->array_jml_kata_per_sentence);
//        $this->hilangkanStopWords();
        $total = count($this->array_token);
        for($i=0;$i<$total;$i++)
        {
            Pembantu::masukkanElemenUnikTerurut($this->array_token_bersih, $this->array_token[$i],$this->array_stopwords,$this->array_index_alph_stopwords);            
        }
    }

/* aboubakr 210113 : Unused
    public function hilangkanStopWords()   //menghilangkan stopwords dari array_token, hasil disimpan dalam array_token_bersih.
    {
//echo '[abouDebug]....Menghilangkan Stopwords....<br/>';
        for($i=0 ; $i < count($this->array_token); $i++)
        {
            $tambahkan_token = true;            //secara default akan memasukkan token.
            $token = $this->array_token[$i];
            if( ((($token[0] >= 'a')&&($token[0] <= 'z')) || (($token[0] >= 'A')&&($token[0] <= 'Z'))) )   // lewati special characters.
            {
                $CC = $token[0];    // ambil karakter pertama dari token. cari di list stopwords yang berawalan karakter sama.
                if(isset($this->array_index_alph_stopwords[$CC]))
                {
                    $j = $this->array_index_alph_stopwords[$CC];
                    $panjang_array_stopwords = count($this->array_stopwords);

                    while(($this->array_stopwords[$j][0] == $CC) && ($j < $panjang_array_stopwords))     // memastikan dalam rentang stopwords yang berawalan sama. dan index masih di dalam rentang array stopwords.
                    {
                        if(!strcasecmp($this->array_stopwords[$j],$token))    // menemukan kata stopwords.
                        {
                            $tambahkan_token = false;
                            break;
                        }
                        $j++;
                    }
                }
                if($tambahkan_token)    // jika flag $tambahkan_token == true.
                {
                    array_push($this->array_token_non_stopwords, $token);
                }
            }
        }
    }
*/
    
}
?>
