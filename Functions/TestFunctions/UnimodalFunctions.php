<?php

//namespace Functions\TestFunctions;

//use Utils\Randomizers;

interface UnimodalFunctionsInterface
{
    function unimodal($variables);
}

/**
 * Dimension: 30
 * Range: [-100, 100]
 * fmin = 0
 */
class UnimodalF1 implements UnimodalFunctionsInterface
{
    function unimodal($populations)
    {
        foreach ($populations as $variable) {
            $results[] = pow(floatval($variable), 2);
        }
        return array_sum($results);
    }
}

/**
 * Dimension: 30
 * Range: [-10, 10]
 * fmin = 0
 */
class UnimodalF2 implements UnimodalFunctionsInterface
{
    function unimodal($variables)
    {
        foreach ($variables as $variable) {
            $absoluteNumbers[] = $variable;
        }

        foreach ($variables as $variable) {
            $results[] = abs($variable) + array_product($absoluteNumbers);
        }
        $absoluteNumbers = [];
        if (is_infinite(array_sum($results)) || array_sum($results) == 0) {
            return 0.00000001;
        }
        return array_sum($results);
    }
}

/**
 * Dimension: 30
 * Range: [-100, 100]
 * fmin = 0
 */
class UnimodalF3 implements UnimodalFunctionsInterface
{
    function unimodal($variables)
    {
        foreach (array_keys($variables) as $key) {
            for ($i = 0; $i <= $key; $i++) {
                $powers[] = pow(floatval($variables[$i]), 2);
            }
            $results[] = array_sum($powers);
            $powers = [];
        }
        return array_sum($results);
    }
}

/**
 * Dimension: 30
 * Range: [-100, 100]
 * fmin = 0
 */
class UnimodalF4 implements UnimodalFunctionsInterface
{
    function unimodal($variables)
    {
        foreach ($variables as $variable) {
            $absoluteNumbers[] = abs($variable);
        }
        return max($absoluteNumbers);
    }
}
class UnimodalF5 implements UnimodalFunctionsInterface
{
    function unimodal($variables)
    {
        foreach ($variables as $key => $variable) {
            if ($key < (count($variables) - 1)) {
                $results[] = 100 * pow($variables[$key + 1] - pow($variable, 2), 2) + pow($variable - 1, 2);
            }
        }
        return array_sum($results);
    }
}
class UnimodalF6 implements UnimodalFunctionsInterface
{
    function unimodal($variables)
    {
        foreach ($variables as $variable) {
            $results[] = pow(($variable + 0.5), 2);
        }
        return array_sum($results);
    }
}
class UnimodalF7 implements UnimodalFunctionsInterface
{
    function unimodal($variables)
    {
        foreach ($variables as $key => $variable){
            $key = $key + 1;
            $results[] = $key * pow($variable, 4) + (new Randomizers())->randomZeroToOneFraction();
        }
        return array_sum($results);
    }
}

class UnimodalFunctionsFactory
{
    public function initializingUnimodalFunctions($function)
    {
        $functions = [
            ['function' => 'f1', 'select' => new UnimodalF1],
            ['function' => 'f2', 'select' => new UnimodalF2],
            ['function' => 'f3', 'select' => new UnimodalF3],
            ['function' => 'f4', 'select' => new UnimodalF4],
            ['function' => 'f5', 'select' => new UnimodalF5],
            ['function' => 'f6', 'select' => new UnimodalF6],
            ['function' => 'f7', 'select' => new UnimodalF7],
        ];
        $index = array_search($function, array_column($functions, 'function'));
        return $functions[$index]['select'];
    }
}
