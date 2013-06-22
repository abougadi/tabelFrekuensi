<?php
    $array_hasil_sim_score_urut = array();
    $array_hasil_sim_score_urut['simScore'] = array(4,123,3,40,56,7);
    $array_hasil_sim_score_urut['string'] = array("Empat","Seratus Dua Puluh Tiga","Tiga","Empat Puluh","Lima Puluh Enam","Tujuh");

    array_multisort($array_hasil_sim_score_urut['simScore'], SORT_DESC, $array_hasil_sim_score_urut['string']);

    for($i=0; $i < count($array_hasil_sim_score_urut['simScore']); $i++)
    {
        echo $array_hasil_sim_score_urut['simScore'][$i] . " - " . $array_hasil_sim_score_urut['string'][$i] . '<br/>';
    }
?>
