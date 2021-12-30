<?php

class ExcessLimit
{
    function cutVariableLimit($function, $variables)
    {
        $variable = (new Variables())->initializeVariableFactory($function);
        $normal = $variable->getVariables('');

        foreach ($variables as $key => $var){
            if ($var < $normal['ranges'][$key]['lowerBound']){
                $variables[$key] = $normal['ranges'][$key]['lowerBound'];
            }
            if ($var > $normal['ranges'][$key]['upperBound']) {
                $variables[$key] = $normal['ranges'][$key]['upperBound'];
            }
        }
        return $variables;
    }
}