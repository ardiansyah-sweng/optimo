<?php

class WalkingMovementStrategy
{
    function highWalking(array $bestReptile, array $eta, array $reduce)
    {
        foreach ($eta as $key1 => $vals){
            foreach ($vals as $key2 => $val){
                $results[] = floatval($bestReptile['individu'][$key2]) * floatval($val) * 0.1  - $reduce[$key1][$key2] * rand(0,1);
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }

    function bellyWalking($bestReptile, $population, $ES)
    {

        foreach ($population as $pop){
            $r1 = (new Randomizers())->getRandomIndexOfIndividu(count($population));
            $x_r1j = $population[$r1];
            foreach ($x_r1j['individu'] as $key => $val){
                $results[] = $bestReptile['individu'][$key] * $val * $ES * rand(0,1);
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }

    function huntingCoordination($bestReptile, $percentageDiff)
    {
        foreach ($percentageDiff as $vals){
            foreach ($vals as $key => $val){
                $results[] = $bestReptile['individu'][$key] * $val * rand(0,1);
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }

    function huntingCooperation($bestReptile, $eta, $reduce)
    {
        foreach ($eta as $key1 => $vals){
            foreach ($vals as $key2 => $val){
                $results[] = $bestReptile['individu'][$key2] - $val * 0.0000001 - $reduce[$key1][$key2] * rand(0,1);
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }
}