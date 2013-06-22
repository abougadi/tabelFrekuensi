<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Umum
 *
 * @author Admin
 */
class Umum {
    /*************/
    /* KONSTANTA */
    /*************/
    //input
    const FOLDER_ACUAN                  = "./Input_mod_1_2/FileAcuan/";
    const FOLDER_UNIT                   = "./Input_mod_1_2/FileUnit/";
    const AKHIRAN_EDU_FILE              = ".txt.chp.edu";
    const AKHIRAN_NERPOS_FILE           = "-NERPOS.txt";
    const ALAMAT_FILE_STOPWORDS         = "./StopWords/stop_words.txt";

    const FOLDER_MASUKAN_MODUL_3        = "./Input_mod3";
    const FOLDER_MASUKAN_MODUL_3_LT     = "/list_token/";
    const FOLDER_MASUKAN_MODUL_3_TF     = "/tabel_frekuensi/";

    const FOLDER_MASUKAN_MODUL_4        = "./Input_mod4";
    const FOLDER_MASUKAN_MODUL_4_IUF    = "/IUF/";
    const FOLDER_MASUKAN_MODUL_4_TF     = "/tabel_frekuensi/";
    const AKHIRAN_MASUKAN_MODUL_4_IUF   = "IUF.txt";

    const FOLDER_MASUKAN_MODUL_5        = "./Input_mod5";
    const FOLDER_MASUKAN_MODUL_5_ACUAN  = "/Acuan/";
    const FOLDER_MASUKAN_MODUL_5_TARGET = "/Target/";

    const FOLDER_MASUKAN_SELEKSI_UNIT   = "./Input_seleksi_unit/";

    const FOLDER_MASUKAN_MODUL_6        = "./Input_mod6";
    const FOLDER_MASUKAN_MODUL_6_STRUNIT= "/StringUnit/";
    const FOLDER_MASUKAN_MODUL_6_SIMSCR = "/SimScore/";
    const FOLDER_MASUKAN_MODUL_6_ACUAN  = "/Acuan/";

    //output
    const FOLDER_HASIL_RINGKASAN        = "./Ringkasan";                    //penyimpanan hasil untuk ringkasan.
    const FOLDER_HASIL_1                = "./Hasil_mod1";                   //penyimpanan hasil untuk modul 1, ditambahkan dengan timestamp.
    const FOLDER_HASIL_2                = "./Hasil_mod2";                   //penyimpanan hasil untuk modul 2, ditambahkan dengan timestamp.
    const FOLDER_HASIL_3                = "./Hasil_mod3";                   //penyimpanan hasil untuk modul 3, ditambahkan dengan timestamp.
    const FOLDER_HASIL_4                = "./Hasil_mod4";                   //penyimpanan hasil untuk modul 4, ditambahkan dengan timestamp.
    const FOLDER_HASIL_5                = "./Hasil_mod5";                   //penyimpanan hasil untuk modul 5, ditambahkan dengan timestamp.
    const FOLDER_HASIL_6                = "./Hasil_mod6";                   //penyimpanan hasil untuk modul 6, ditambahkan dengan timestamp.
    const FOLDER_HASIL_SELEKSI_UNIT     = "./Hasil_seleksiUnit";            //penyimpanan hasil untuk modul seleksiUnit, ditambahkan dengan timestamp.
    const AKHIRAN_HASIL_UBAH_FORMAT     = "_ubahFormat";

    const AKHIRAN_FILE_RINGKASAN_TOK    = "-countTok.txt";
    const AKHIRAN_FILE_RINGKASAN_ALL    = "-countAll.txt";
    const FOLDER_LIST_TOKEN             = "/ListToken/";
    const AKHIRAN_LIST_TOKEN            = "-listToken.txt";
    const NAMAFILE_LIST_TOKEN_MOD2      = "SemuaFileUnit-listToken.txt";    //tempat menyimpan list token seluruh file unit, hasil proses pada modul 2.
    const FOLDER_TABEL_FREQ_STC         = "/FreqTabel-SD/";
    const AKHIRAN_TABEL_FREQ_STC        = "-tf-SD.txt";
    const NAMAFILE_TABEL_FREQ_MOD2_STC  = "TabelFreq_SemuaFileUnit-tf-SD.txt";
    const FOLDER_TABEL_FREQ_EDU         = "/FreqTabel-ED/";
    const AKHIRAN_TABEL_FREQ_EDU        = "-tf-ED.txt";
    const NAMAFILE_TABEL_FREQ_MOD2_EDU  = "TabelFreq_SemuaFileUnit-tf-ED.txt";
    const PENGHUBUNG_NAMAFILE           = "_LS_";
    const AKHIRAN_TABEL_FREQ_DOC        = "-tf-DD.txt";

    const AKHIRAN_HASIL_MOD3_EDU        = "-TFIEF-ED.txt";
    const AKHIRAN_HASIL_MOD3_STC        = "-TFIEF-SD.txt";
    const AKHIRAN_HASIL_MOD3_DOC        = "-TFIEF-DD.txt";
    const NAMAFILE_HASIL_MOD3_SEMUA_EDU = "All-TFIEF-ED.txt";
    const NAMAFILE_HASIL_MOD3_SEMUA_STC = "All-TFISF-SD.txt";
    const NAMAFILE_HASIL_MOD3_SEMUA_DOC = "All-TFIDF-DD.txt";
    const NAMAFILE_HASIL_MOD3_LEMMA_TF  = "lemma-TF-summary.txt";
    const NAMAFILE_HASIL_MOD3_LEMMA_IUF = "lemma-IUF.txt";
    const NAMAFILE_HASIL_MOD3_IUF       = "IUF.txt";

    const AKHIRAN_HASIL_MOD4_EDU        = "-NormW-ED.txt";
    const AKHIRAN_HASIL_MOD4_STC        = "-NormW-SD.txt";
    const AKHIRAN_HASIL_MOD4_DOC        = "-NormW-DD.txt";
    const NAMAFILE_HASIL_MOD4_SEMUA_EDU = "All-NormW-ED.txt";
    const NAMAFILE_HASIL_MOD4_SEMUA_STC = "All-NormW_SD.txt";
    const NAMAFILE_HASIL_MOD4_SEMUA_DOC = "All-NormW_DD.txt";

    const AKHIRAN_HASIL_MOD5_EDU        = "-SimS-ED.txt";
    const AKHIRAN_HASIL_MOD5_STC        = "-SimS-SD.txt";
    const AKHIRAN_HASIL_MOD5_DOC        = "-SimS-DD.txt";

    const FOLDER_FILTERED_TOKEN         = "/FilteredToken/";
    const FOLDER_FILTERED_FREQ_TABLE    = "/FilteredFreqTable/";
    const FOLDER_FILTERED_FREQ_TABLE_TR = "/FilteredFreqTableTranspose/";
    const FOLDER_LIST_MULTIPLE_UNIT     = "/ListMultipleEDU/";
    const AKHIRAN_HASIL_SELEKSI_UNIT    = "-Flist.txt";

    const AKHIRAN_INPUT_MOD6_EDU        = "-ED.txt";
    const AKHIRAN_INPUT_MOD6_STC        = "-SD.txt";
    const AKHIRAN_INPUT_MOD6_DOC        = "-DD.txt";

    // Konstanta SeleksiUnit
    // penanda filter token dan NER, nilai-nilai penanda sesuai urutan, mulai dari 2^0 :: TANPA_KRITERIA, LEMMA, NN (semua POS noun), VB (semua POS verb), JJ, PRP, PERSON, LOCATION, ORGANIZATION, DATE, MONEY, TIME, NUMBER, ORDINAL, MISC.
    const FILTER_TANPA_KRITERIA         = 1;        // 2^0
    const FILTER_LEMMA                  = 2;        // 2^1
    const FILTER_POS_NN                 = 4;        // 2^2
    const FILTER_POS_VB                 = 8;        // 2^3
    const FILTER_POS_JJ                 = 16;       // 2^4
    const FILTER_POS_PRP                = 32;       // 2^5
    const FILTER_NER_PERSON             = 64;       // 2^6
    const FILTER_NER_LOCATION           = 128;      // 2^7
    const FILTER_NER_ORGANIZATION       = 256;      // 2^8
    const FILTER_NER_DATE               = 512;      // 2^9
    const FILTER_NER_MONEY              = 1024;     // 2^10
    const FILTER_NER_TIME               = 2048;     // 2^11
    const FILTER_NER_NUMBER             = 4096;     // 2^12
    const FILTER_NER_ORDINAL            = 8192;     // 2^13
    const FILTER_NER_MISC               = 16384;    // 2^14

    const FILTER_MASK_POS               = 60;       //(self::FILTER_POS_JJ | self::FILTER_POS_NN | self::FILTER_POS_VB | self::FILTER_POS_PRP);
    const FILTER_MASK_NER               = 32704;    //(self::FILTER_NER_PERSON | self::FILTER_NER_LOCATION | self::FILTER_NER_ORGANIZATION | self::FILTER_NER_DATE | self::FILTER_NER_MONEY | self::FILTER_NER_TIME | self::FILTER_NER_NUMBER | self::FILTER_NER_ORDINAL | self::FILTER_NER_MISC);

//    penanda unit string, nilai-nilai penanda sesuai urutan, mulai dari 0 :: DOKUMEN, SENTENCE, SINGLE_EDU, DOUBLE_EDU, TRIPLE_EDU.
    const UNIT_STRING_DOKUMEN           = 0;
    const UNIT_STRING_SENTENCE          = 1;
    const UNIT_STRING_SINGLE_EDU        = 2;
    const UNIT_STRING_DOUBLE_EDU        = 3;
    const UNIT_STRING_TRIPLE_EDU        = 4;

}
?>
