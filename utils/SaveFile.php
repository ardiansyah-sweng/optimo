<?php

class FileSaver
{
    function saveToFile($path, $data)
    {
        $fp = fopen($path, 'a');
        fputcsv($fp, $data);
        fclose($fp);
    }
}
