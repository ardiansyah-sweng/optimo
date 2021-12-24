<?php

namespace Functions\TestFunctions;

use Utils\Randomizers;

interface MultimodalFunctionsInterface
{
    function multimodal($variables);
}

/**
 * Dimension: 30
 * Range: [-500, 500]
 * fmin = 0
 */
class MultimodalF8 implements MultimodalFunctionsInterface
{
    function multimodal($variables)
    {
        foreach ($variables as $variable) {
            $results[] = -$variable * sin(sqrt(abs($variable)));
        }
        return array_sum($results);
    }
}

/**
 * Dimension: 30
 * Range: [-5.12, 5.12]
 * fmin = 0
 */
class MultimodalF9 implements MultimodalFunctionsInterface
{
    function multimodal($variables)
    {
        foreach ($variables as $variable) {
            $results[] = pow($variable, 2) - 10 * cos(2 * pi() * $variable) + 10;
        }
        return array_sum($results);
    }
}

/**
 * Dimension: 30
 * Range: [-32, 32]
 * fmin = 0
 */
class MultimodalF10 implements MultimodalFunctionsInterface
{  #ULANG!
    function multimodal($variables)
    {
        foreach ($variables as $variable){
            $results1[] = pow($variable, 2);
            $results2[] = cos(2 * pi() * $variable );
        }
        return -20 * exp(-0.2 * sqrt(1/count($variables)) * array_sum($results1) ) - exp( (1/count($variables)) * array_sum($results2) ) + 20 + M_EULER;
    }
}

/**
 * Dimension: 30
 * Range: [-600, 600]
 * fmin = 0
 */
class MultimodalF11 implements MultimodalFunctionsInterface
{
    function multimodal($variables)
    {
        foreach ($variables as $key => $variable) {
            $key = $key + 1;
            $results1[] = pow($variable, 2);
            $results2[] = cos( ($variable/(sqrt($key))) ) + 1;
        }

        return (1/4000) * array_sum($results1) - array_product($results2);
    }
}

/**
 * Dimension: 30
 * Range: [-50, 50]
 * fmin = 0
 */
class MultimodalF12 implements MultimodalFunctionsInterface
{
    function y($variable)
    {
        return 1 + ( ($variable + 1)/4 );
    }

    function u($variable, $a, $k, $m)
    {
        $one = $k * pow( ($variable - $a), $m) * $variable;
        $two = $k * pow( ( -$variable - $a ), $m) * $variable;

        if ($one > $a){
            return $one;
        }
        if ($two < $a){
            return $two;
        }
        if ($variable > -$a && $variable < $a){
            return 0;
        }
    }

    function multimodal($variables)
    {
        $a = 10;
        $k = 100;
        $m = 4;
        foreach ($variables as $key => $variable) {
            if ($key < count($variables)-1){
                $results1[] = pow($this->y($variable), 2) * ( 1 + 10 * pow( sin( (pi() * $this->y($variables[$key+1]) ) ),2 ) );
                $results2[] = $this->u($variable, $a, $k, $m);
            }
        }
        $y1 = $this->y($variables[0]);
        return ( pi()/count($variables) ) * ( 10 * sin( pi() * $y1) ) + array_sum($results1) + array_sum($results2);
    }
}

/**
 * Dimension: 30
 * Range: [-50, 50]
 * fmin = 0
 */
class MultimodalF13 implements MultimodalFunctionsInterface
{
    function multimodal($variables)
    {
        $u = new MultimodalF12;
        $a = 5;
        $k = 100;
        $m = 4;
        foreach ($variables as $variable){
            $results1[] = pow($variable-1, 2) * ( 1 + pow( sin( 3*pi()*$variable + 1 ), 2 )) + pow($variables[count($variables)-1] - 1, 2) * pow(1 + sin(2 * pi() * $variables[count($variables)-1]), 2); 

            $results2[] = $u->u($variable, $a, $k, $m);
        }
        return 0.1 * ( pow( 3*pi()*$variables[0] ,2) ) + array_sum($results1) + array_sum($results2);
    }
}

class MultimodalFunctionsFactory
{
    public function initializingMultimodalFunctions($function)
    {
        $functions = [
            ['function' => 'f8', 'select' => new MultimodalF8],
            ['function' => 'f9', 'select' => new MultimodalF9],
            ['function' => 'f10', 'select' => new MultimodalF10],
            ['function' => 'f11', 'select' => new MultimodalF11],
            ['function' => 'f12', 'select' => new MultimodalF12],
            ['function' => 'f13', 'select' => new MultimodalF13]
        ];
        $index = array_search($function, array_column($functions, 'function'));
        return $functions[$index]['select'];
    }
}
