<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Tabel Frekuensi V2 :: Per Modul</title>
        <style type="text/css">
            h2{
                color: blue;
                font-family: cursive;
                font-size: 13pt;
            }
            .module_description{
                
            }
            .execution{
                text-align: center;
                background-color: antiquewhite;
            }
            .module_container{
                border: black double;
            }
        </style>
        <script type="text/javascript">
            var kriteriaPilihan = new Array();

            function modul1()
            {
                redirectPage('./modul1.php');
            }

            function modul2()
            {
                redirectPage('./modul2.php');
            }

            function modul6()
            {
                redirectPage('./modul6.php');
            }

            function ubahFormat()
            {
                redirectPage('./ubahFormat.php?folder=' + document.getElementById('folder').value);
            }

            function redirectPage(target)
            {
                window.location = target;
            }

            function getGolonganKriteria(kriteria)
            {
                if(kriteria <= 2)       // No filter
                {
                    return 0;
                }
                else if(kriteria <= 32 && kriteria >= 4)    // POS Tag Filter
                {
                    return 1;
                }
                else        // NER Tag Filter
                {
                    return 2;
                }
            }

            function resetKriteria(selIndex)
            {
                var kriteriaDropdown = document.getElementById("kriteria");

                for(var i=0; i < kriteriaDropdown.options.length; i++)
                {
                    if(i != selIndex)
                    {
                        kriteriaDropdown.options[i].selected = false;
                    }
                }
            }

            var golKriteriaAktif = 0;
            function selKriteria(kriteriaBaru,selIndex)
            {
                var golKriteriaBaru = getGolonganKriteria(kriteriaBaru);

                if(golKriteriaAktif != golKriteriaBaru || golKriteriaAktif == 0)
                {
                    resetKriteria(selIndex);
                    golKriteriaAktif = golKriteriaBaru;
                }
            }
        </script>
    </head>
    <body>
        <h2>##Modul 1 : Tabel Kemunculan kata atau token berdasar File Acuan.</h2>
        <div class="module_container">
        <div id="modul1_desc" class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
                        <li>File Acuan (path <b>'./input_mod_1_2/FileAcuan/'</b>) : <b><u>Sepasang</u></b> file edu dan NERPOS. Format penamaan "<b>File1<span style="color:red;">.txt.chp.edu</span></b>" dan "<b>File1<span style="color:red;">-NERPOS.txt</span></b>" (gunakan <b>HANYA</b> satu pasang file edu dan NERPOS yang sesuai, karena yang akan dipakai hanya satu pasang file pertama.)</li>
                        <li>File Unit (path <b>'./input_mod_1_2/FileUnit/'</b>) : Beberapa pasang file edu dan NERPOS. Format penamaan "<b>File1<span style="color:red;">.txt.chp.edu</span></b>" dan "<b>File1<span style="color:red;">-NERPOS.txt</span></b>"</li>
                        <li>File StopWords (path <b>'./StopWords/stop_words.txt'</b>): Kumpulan kata-kata yang digunakan sebagai stop words.</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>ListToken (path <b>'./Hasil_mod1_[TIMESTAMP]/ListToken/'</b>) : Berisi list token yang digunakan sebagai acuan dalam proses pembuatan tabel frekuensi. Format penamaan "<b>NamaFileAcuan<span style="color:red;">-listToken.txt</span></b>".</li>
                        <li>Tabel Frekuensi per Sentence (path <b>'./Hasil_mod1_[TIMESTAMP]/FreqTabel-SD/'</b>) : Berisi nilai frekuensi token (dari ListToken) untuk setiap unit string <u>sentence</u> pada tiap file pada folder FileUnit. Format penamaan "<b>NamaFileAcuan<span style="color:red;">_LS_</span>NamaFileUnit<span style="color:red;">-tf-SD.txt</span></b>".</li>
                        <li>Tabel Frekuensi per Sentence (path <b>'./Hasil_mod1_[TIMESTAMP]/FreqTabel-ED/'</b>) : Berisi nilai frekuensi token (dari ListToken) untuk setiap unit string <u>EDU</u> pada tiap file pada folder FileUnit. Format penamaan "<b>NamaFileAcuan<span style="color:red;">_LS_</span>NamaFileUnit<span style="color:red;">-tf-ED.txt</span></b>".</li>
                    </ul>
                </span>
        </div>
        <div  id="modul1_exe" class="execution">
            <button onclick="modul1();">Modul #1</button>
        </div>
        </div>
        <h2>##Modul 2 : Tabel Kemunculan token dari seluruh FileNews (dibawah 1 folder).</h2>
        <div class="module_container">
        <div  id="modul2_desc" class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
<!--                        <li>File Acuan (path <b>'./input_mod_1_2/FileAcuan/'</b>) : <b><u>Sepasang</u></b> file edu dan NERPOS. Format penamaan "<b>File1<span style="color:red;">.txt.chp.edu</span></b>" dan "<b>File1<span style="color:red;">-NERPOS.txt</span></b>" (gunakan <b>HANYA</b> satu pasang file edu dan NERPOS yang sesuai, karena yang akan dipakai hanya satu pasang file pertama.)</li>    -->
                        <li>File Unit (path <b>'./input_mod_1_2/FileUnit/'</b>) : Beberapa pasang file edu dan NERPOS. Format penamaan "<b>File1<span style="color:red;">.txt.chp.edu</span></b>" dan "<b>File1<span style="color:red;">-NERPOS.txt</span></b>"</li>
                        <li>File StopWords (path <b>'./StopWords/stop_words.txt'</b>): Kumpulan kata-kata yang digunakan sebagai stop words.</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>ListToken (path <b>'./Hasil_mod2_[TIMESTAMP]/ListToken/'</b>) : Berisi list token yang digunakan sebagai acuan dalam proses pembuatan tabel frekuensi. Format penamaan "<b><span style="color:red;">SemuaFileUnit-listToken.txt</span></b>".</li>
                        <li>Tabel Frekuensi per Sentence (path <b>'./Hasil_mod2_[TIMESTAMP]/FreqTabel-SD/'</b>) : Berisi nilai frekuensi token (dari ListToken) untuk setiap unit string <u>sentence</u> pada tiap file pada folder FileUnit. Format penamaan "<b><span style="color:red;">TabelFreq_SemuaFileUnit-tf-SD.txt</span></b>".</li>
                        <li>Tabel Frekuensi per Sentence (path <b>'./Hasil_mod2_[TIMESTAMP]/FreqTabel-ED/'</b>) : Berisi nilai frekuensi token (dari ListToken) untuk setiap unit string <u>EDU</u> pada tiap file pada folder FileUnit. Format penamaan "<b><span style="color:red;">TabelFreq_SemuaFileUnit-tf-ED.txt</span></b>".</li>
                    </ul>
                </span>
        </div>
        <div  id="modul2_exe" class="execution">
            <button onclick="modul2();">Modul #2</button>
        </div>
        </div>
        <h2>##Modul 3 : Tabel TF-IUF.</h2>
        <div class="module_container">
        <div  id="modul3_desc" class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
                        <li>File Tabel Frekuensi (path <b>'./input_mod_3/tabel_frekuensi/'</b>) : Beberapa file dengan format sesuai dengan format hasil keluaran modul 1.</li>
                        <li>File List Token (path <b>'./input_mod_3/list_token/'</b>) : Sebuah file yang berisi list token yang dipakai untuk mendapatkan hasil seperti yang digunakan pada folder input Tabel Frekuensi di modul ini.</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>Hasil Perhitungan TFIUF Keseluruhan File (path <b>'./Hasil_mod3_[TIMESTAMP]/<span style="color:red;">All-<span style="color:black;">TFIUF-UF</span>.txt</span>'</b>) : UF --> sesuai dengan unit string, DD (document), SD (sentence), atau ED (EDU).</li>
                        <li>Hasil Perhitungan TFIUF Untuk Tiap File (path <b>'./Hasil_mod3_[TIMESTAMP]/NamaFileTabelFrekuensi-TFIUF-UF.txt'</b>) : UF --> sesuai dengan unit string, DD (document), SD (sentence), atau ED (EDU). NamaFileInput sesuai dengan setiap file yang ada di folder input.</li>
                        <li>Nilai IUF (path <b>'./Hasil_mod3_[TIMESTAMP]/IUF.txt'</b>)</li>
                        <li>Nilai IUF dengan lemma (path <b>'./Hasil_mod3_[TIMESTAMP]/lemma-IUF.txt'</b>)</li>
                        <li>Summary TabelFrekuensi Untuk ListToken yang Digunakan (path <b>'./Hasil_mod3_[TIMESTAMP]/lemma-TF-summary.txt'</b>)</li>
                    </ul>
                </span>
        </div>
        <div  id="modul3_exe" class="execution">
            <form method="GET" action="modul3.php">
            <label for="tambah_satu">Divider + 1 ? </label>
            <input type="radio" name="tambah_satu" id="tambah_satu" value="true" checked="checked"/>Ya&nbsp;
            <input type="radio" name="tambah_satu" id="tambah_satu" value="false"/>Tidak<br/>
<!--        Tidak perlu konfirmasi, langsung detect dari nama file.
            <label for="per_folder">IDF (proses seluruh dokumen per-folder) ?</label>
            <input type="radio" name="per_folder" id="per_folder" value="true" checked="checked"/>Ya&nbsp;
            <input type="radio" name="per_folder" id="per_folder" value="false"/>Tidak<br/>
-->
            <input type="submit" value="Modul #3"/>
            </form>
        </div>
        </div>
        <h2>##Modul 4 : Tabel Normal Weight.</h2>
        <div class="module_container">
        <div class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
                        <li>File Tabel Frekuensi (path <b>'./input_mod_4/tabel_frekuensi'</b>) : Beberapa file dengan format sesuai dengan format hasil keluaran modul 1.</li>
                        <li>File IUF (path <b>'./input_mod_4/IUF/IUF.txt'</b>) : Sebuah file yang berisi nilai IUF dari modul #3.</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>Hasil Perhitungan NormalWeight Keseluruhan File (path <b>'./Hasil_mod4_[TIMESTAMP]/<span style="color:red;">All-NormW-<span style="color:black;">UF</span>.txt</span>'</b>) : UF --> sesuai dengan unit string, DD (document), SD (sentence), atau ED (EDU).</li>
                        <li>Hasil Perhitungan NormalWeight Untuk Tiap File (path <b>'./Hasil_mod4_[TIMESTAMP]/<span style="color:red;">NamaFileInput-<span style="color:black;">NormW</span>-UF.txt</span>'</b>) : UF --> sesuai dengan unit string, DD (document), SD (sentence), atau ED (EDU). NamaFileInput sesuai dengan setiap file yang ada di folder input.</li>
                    </ul>
                </span>
        </div>
        <div  id="modul4_exe" class="execution">
            <form method="GET" action="modul4.php">
            <label for="tambah_satu">Divider + 1 ? </label>
            <input type="radio" name="tambah_satu" id="tambah_satu" value="true" checked="checked"/>Ya&nbsp;
            <input type="radio" name="tambah_satu" id="tambah_satu" value="false"/>Tidak<br/>
            <input type="submit" value="Modul #4"/>
            </form>
        </div>
        </div>
        <h2>##Modul 5 : Similarity Score.</h2>
        <div class="module_container">
        <div class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
                        <li>File Acuan (path <b>'./input_mod_5/Acuan/'</b>) : Berisi satu file matriks tabel frekuensi (dari hasil modul #1) yang digunakan acuan.</li>
                        <li>File Target (path <b>'./input_mod_5/Target/'</b>) : Berisi beberapa file matriks tabel frekuensi (dari hasil modul #1) yang digunakan sebagai target perhitungan similarity score.</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>File Hasil Perhitungan Similarity Score (path <b>'./Hasil_mod5_[TIMESTAMP]/<span style="color:red;">#<span style="color:black;">[URUTAN_DOKUMEN_TARGET]</span>_<span style="color:black;">[NOMOR_BARIS_ACUAN]</span><span style="color:black;">NamaFileAcuan</span>__<span style="color:black;">NamaFileTarget</span>-SimS-UF.txt</span>'</b>) : Berisi hasil perhitungan similarity score antara file target dengan tiap baris nilai matriks pada file acuan. (dijadikan file terpisah). UF --> disesuaikan sesuai unit string file input.</li>
                    </ul>
                </span>
        </div>
        <div  id="modul5_exe" class="execution">
            <form method="GET" action="modul5.php">
            <label for="tambah_satu">Divider + 1 ? </label>
            <input type="radio" name="tambah_satu" id="tambah_satu" value="true" checked="checked"/>Ya&nbsp;
            <input type="radio" name="tambah_satu" id="tambah_satu" value="false"/>Tidak<br/>
            <input type="submit" value="Modul #5"/>
            </form>
        </div>
        </div>
        <h2>##Modul 6 : Lookup: Similarity Score dengan String-Unit.</h2>
        <div class="module_container">
        <div class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
                        <li>File Acuan (path <b>'./input_mod_6/Acuan/'</b>) : Berisi satu file matriks tabel frekuensi (dari hasil modul #1) yang digunakan acuan.</li>
                        <li>File SimScore (path <b>'./input_mod_6/SimScore/'</b>) : Berisi beberapa file hasil dari modul #5.</li>
                        <li>File String Unit (path <b>'./input_mod_6/StringUnit/'</b>) : Berisi beberapa pasang file EDU dan NERPOS, digunakan untuk mengambil String tiap unitnya.</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>File Hasil Pengurutan Similarity Score (path <b>'./Hasil_mod6_[TIMESTAMP]/NamaFileAcuan-SimSsrt-UF.txt'</b>) : Berisi hasil pengurutan nilai similarity Score, beserta string unitnya. UF --> disesuaikan sesuai unit string file input.</li>
                    </ul>
                </span>
        </div>
        <div  id="modul6_exe" class="execution">
            <form method="GET" action="modul6.php">
            <label for="gen_file_individu">Generate masing2 file unit? </label>
            <input type="radio" name="gen_file_individu" id="gen_file_individu" value="true"/>Ya&nbsp;
            <input type="radio" name="gen_file_individu" id="gen_file_individu" value="false"  checked="checked"/>Tidak<br/>
            <input type="submit" value="Modul #6"/>
            </form>
<!--            <button onclick="modul6();">Modul #6</button>   -->
        </div>
        </div>
        <h2>##Modul Seleksi Unit : Filter Unit Berdasarkan Kriteria & Unit String.</h2>
        <div class="module_container">
        <div class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
                        <li>File Unit (path <b>'./input_seleksi_unit/'</b>) : Berisi beberapa pasang file EDU dan NERPOS. Proses mirip modul 1, namun menggunakan filtering berdasarkan kriteria dan unit string.</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>Filtered ListToken (path <b>'./Hasil_seleksiUnit_[TIMESTAMP]/FilteredToken/'</b>) : Berisi list token yang telah difilter dari tiap file masukan. Format penamaan "<b>NamaFileMasukan<span style="color:red;">-Flist.txt</span></b>".</li>
                        <li>Filtered Frequency Table (path <b>'./Hasil_seleksiUnit_[TIMESTAMP]/FilteredFreqTabel/'</b>) : Berisi nilai frekuensi token (dari ListToken) berdasarkan setiap unit string untuk tiap file pada folder masukan. Format penamaan "<b>NamaFileMasukan<span style="color:red;">-tf-UF.txt</span></b>".</li>
                    </ul>
                </span>
        </div>
        <div  id="modulSeleksiUnit_exe" class="execution">
<!--    const UNIT_STRING_DOKUMEN           = 0;
    const UNIT_STRING_SENTENCE          = 1;
    const UNIT_STRING_SINGLE_EDU        = 2;
    const UNIT_STRING_DOUBLE_EDU        = 3;
    const UNIT_STRING_TRIPLE_EDU        = 4;-->
            <br/>
            <span style="color:blue;font-size: small;">(*) <i>Pilih lebih dari satu kriteria dengan menahan tombol Ctrl saat memilih kriteria. (Hanya berlaku untuk POS dan NER saja)</i></span>
            <form method="GET" action="modul_seleksi_unit.php">
                <label for="kriteria">Pilih Kriteria : </label>
                <select id="kriteria" name="kriteria[]" multiple="true" size="10">
                    <optgroup label="=====NO FILTER=====">
                    <option value="1" selected="selected" onclick="selKriteria(this.value,this.index)">Tanpa Kriteria</option>
                    <option value="2" onclick="selKriteria(this.value,this.index)">Lemma</option>
                    </optgroup>
                    <optgroup label="=====POS TAG FILTER=====">
                    <option value="4" onclick="selKriteria(this.value,this.index)">POS NN</option>
                    <option value="8" onclick="selKriteria(this.value,this.index)">POS VB</option>
                    <option value="16" onclick="selKriteria(this.value,this.index)">POS JJ</option>
                    <option value="32" onclick="selKriteria(this.value,this.index)">POS PRP</option>
                    </optgroup>
                    <optgroup label="=====NER TAG FILTER=====">
                    <option value="64" onclick="selKriteria(this.value,this.index)">NER PERSON</option>
                    <option value="128" onclick="selKriteria(this.value,this.index)">NER LOCATION</option>
                    <option value="256" onclick="selKriteria(this.value,this.index)">NER ORGANIZATION</option>
                    <option value="512" onclick="selKriteria(this.value,this.index)">NER DATE</option>
                    <option value="1024" onclick="selKriteria(this.value,this.index)">NER MONEY</option>
                    <option value="2048" onclick="selKriteria(this.value,this.index)">NER TIME</option>
                    <option value="4096" onclick="selKriteria(this.value,this.index)">NER NUMBER</option>
                    <option value="8192" onclick="selKriteria(this.value,this.index)">NER ORDINAL</option>
                    <option value="16384" onclick="selKriteria(this.value,this.index)">NER MISC</option>
                    </optgroup>
                </select>
                <br/>
                <label for="unit_string">Unit String : </label>
                <select id="unit_string" name="unit_string">
                    <option value="0" selected="selected">DOKUMEN (File)</option>
                    <option value="1">Sentence</option>
                    <option value="2">Single EDU</option>
                    <option value="3">2-EDU</option>
                    <option value="4">3-EDU</option>
                </select><br/>
                <label for="debug">Cetak Pesan Debugging ? </label>
                <input type="radio" name="debug" id="debug" value="true" checked="checked"/>Ya&nbsp;
                <input type="radio" name="debug" id="debug" value="false"/>Tidak<br/>

                <input type="submit" value="Modul Seleksi Unit" />
            </form>
        </div>
        </div>
        <h2>##Ubah Format Keluaran ==> Transpose Matriks Hasil.</h2>
        <div class="module_container">
        <div class="module_description">
            <span class="label">Masukan : </span>
                <span class="description">
                    <ul>
                        <li>File2 Matriks hasil pemrosesan. Dalam Folder tertentu. (Posisi relatif terhadap index.php)</li>
                    </ul>
                </span>
            <span class="label">Keluaran : </span>
                <span class="description">
                    <ul>
                        <li>Folder Hasil pengubahan format matriks keluaran. Hasil Transpose matriks input.</li>
                    </ul>
                </span>
        </div>
        <div  id="modul_ubah_format" class="execution">
            <label for="folder">Folder Masukan : </label><input type="input" name="folder" id="folder" />
            <button onclick="ubahFormat();">Ubah Format Keluaran</button>
        </div>
        </div>
        <br/>
    </body>
</html>
