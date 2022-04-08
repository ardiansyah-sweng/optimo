<?php

require 'vendor/autoload.php';

class Randomizers
{
    static function randomZeroToOneFraction()
    {
        return (float) rand() / (float) getrandmax();
    }

    static function randomVariableValueByRange($variableRanges)
    {
        foreach ($variableRanges as $range) {
            if (count($variableRanges) > 1) {
                $ret[] = mt_rand($range['lowerBound'] * 100, $range['upperBound'] * 100) / 100;
            } else {
                for ($i = 0; $i < 30; $i++) {
                    $ret[] = mt_rand($range['lowerBound'] * 100, $range['upperBound'] * 100) / 100;
                }
            }
        }
        return $ret;
    }

    static function randomOneVariable($upperBound, $lowerBound)
    {
        return mt_rand($lowerBound * 100, $upperBound * 100) / 100;
    }

    static function getCutPointIndex($lengthOfChromosome)
    {
        return rand(0, $lengthOfChromosome - 1);
    }

    static function getRandomIndexOfIndividu($popSize)
    {
        return rand(0, $popSize - 1);
    }
}
