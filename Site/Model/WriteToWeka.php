<?php

/**
 * Created by Joakim Nilsson.
 * Date: 23/02/2016
 * Time: 09:04
 */
class WriteToWeka
{

    function generateFile($name,$rating){
        $wekaFile = fopen(dirname(__FILE__) . "/../Resources/WekaAnalyze.txt", 'a+') or die("can't open file");
        fwrite($wekaFile, $name . "," . $rating ."\n");
        fclose($wekaFile);
    }
}