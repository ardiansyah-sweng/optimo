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
}