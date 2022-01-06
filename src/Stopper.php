<?php

class Stopper
{
    public $numOfLastResult;

    function evaluation($iter, $analitics)
    {
        if ($iter >= ($this->numOfLastResult - 1)) {
            $residual = count($analitics) - $this->numOfLastResult;

            if ($residual === 0 && count(array_unique($analitics)) === 1) {
                return true;
            }

            if ($residual > 0) {
                for ($i = 0; $i < $residual; $i++) {
                    array_shift($analitics);
                }
                if (count(array_unique($analitics)) === 1) {
                    return true;
                }
            }
        }
    }

    function evaluationKMA($iter, $analitics)
    {
        if ($iter >= ($this->numOfLastResult - 1)) {
            $residual = count($analitics) - $this->numOfLastResult;

            $fitnessDiff1 = abs($analitics[0] - $analitics[1]) / $analitics[0];
            $fitnessDiff2 = abs($analitics[1] - $analitics[2]) / $analitics[1];

            if ($residual === 0 && ($fitnessDiff1 > 0 && $fitnessDiff2 > 0)) {
                return 'dec';
            }
            if ($residual === 0 && ($fitnessDiff1 == 0 && $fitnessDiff2 == 0)) {
                return 'add';
            } 

            if ($residual > 0) {
                for ($i = 0; $i < $residual; $i++) {
                    array_shift($analitics);
                }
                if (($fitnessDiff1 > 0 && $fitnessDiff2 > 0)) {
                    return 'dec';
                }
                if (($fitnessDiff1 == 0 && $fitnessDiff2 == 0)) {
                    return 'dec';
                }
            }
        }
    }

    function improvementUnderCertainPercentage($baseline, $iter, $analitics)
    {
        // for ($i = 0; $i < 10; $i++){
        //     $randoms[] = rand(1, 10);
        // }

        // foreach ($randoms as $key => $val){
        //     if ( ($key+1) < 10) {
        //         $residual[]  = ($randoms[$key+1] - $val) / $val;
        //     }        
        // }

        if ($iter >= ($this->numOfLastResult - 1)) {
            $residual = count($analitics) - $this->numOfLastResult;

            if ($residual === 0 && count(array_unique($analitics)) === 1) {
                return true;
            }

            if ($residual > 0) {
                for ($i = 0; $i < $residual; $i++) {
                    array_shift($analitics);
                }
                if (count(array_unique($analitics)) === 1) {
                    return true;
                }
            }
        }

        if (array_sum($residual) > $baseline) {
            return true;
        }
    }
}
