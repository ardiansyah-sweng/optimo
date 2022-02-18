<?php

class MinMaxScaler
{
    public $dataset;

    function changeColumn()
    {
        $numOfCol = count($this->dataset[0]);
        for ($i = 0; $i < $numOfCol; $i++) {
            foreach ($this->dataset as $data) {
                $ret[] = $data[$i];
            }
            $rets[] = $ret;
            $ret = [];
        }
        return $rets;
    }

    function getMinMax()
    {
        $numOfCol = count($this->dataset[0]);
        for ($j = 0; $j < $numOfCol; $j++) {
            $minMax[] = [
                'min' => min($this->changeColumn()[$j]),
                'max' => max($this->changeColumn()[$j])
            ];
        }
        return $minMax;
    }

    function normalization()
    {
        $numOfCol = count($this->dataset[0]);
        for ($i = 0; $i < $numOfCol; $i++) {
            foreach ($this->changeColumn()[$i] as $val) {
                $ret[] = ($val - $this->getMinMax()[$i]['min']) / ($this->getMinMax()[$i]['max'] - $this->getMinMax()[$i]['min']);
            }
            $rets[] = $ret;
            $ret = [];
        }

        foreach (array_keys($rets[0]) as $j) {
            for ($k = 0; $k < $numOfCol; $k++) {
                $ret[] = $rets[$k][$j];
            }
            $normalData[] = $ret;
            $ret = [];
        }
        return $normalData;
    }

    function denormalizing($normalDataInput, $normalRes, $actualData)
    {
        foreach ($normalRes as $key1 => $vals) {
            foreach ($vals[0] as $key2 => $val) {
                if ($key2 === 1){
                    $estimated = $actualData[$key1][0] - ($normalDataInput[$key1][0] - $val) *  $actualData[$key1][0];
                    $errors[] = abs($estimated - $actualData[$key1][0]);
                    // echo 'estimated: '.$estimated .' actual '. $actualData[$key1][0]. ' error: '. abs($estimated - $actualData[$key1][0])."\n";
                }
            }
        }
        echo 'Data: '.count($errors);echo"\n";
        return array_sum($errors) / count($errors);
    }
}
