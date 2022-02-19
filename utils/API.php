<?php

class API
{
    function getAPI($kernel, $ret)
    {
        $url = 'http://localhost:8000/count';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ret, 0));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        $result = array_values(json_decode($response, true))[0];
        $results = json_decode($response, true);
        return $result[$kernel];
    }
}
