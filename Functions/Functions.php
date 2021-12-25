<?php
require 'vendor/autoload.php';

//use Functions\TestFunctions\UnimodalFunctionsFactory as Unimodal;
//use Functions\TestFunctions\MultimodalFunctionsFactory as Multimodal;

interface FunctionsInterface
{
    function runFunction($population, $functionType);
}

class FunctionF1 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($population);
    }
}

class FunctionF2 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($population);
    }
}

class FunctionF3 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($population);
    }
}

class FunctionF4 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($population);
    }
}

class FunctionF5 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($population);
    }
}

class FunctionF6 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($population);
    }
}

class FunctionF7 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($population);
    }
}

class FunctionF8 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($population);
    }
}

class FunctionF9 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($population);
    }
}

class FunctionF10 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($population);
    }
}

class FunctionF11 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($population);
    }
}

class FunctionF12 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($population);
    }
}

class FunctionF13 implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($population);
    }
}

class FunctionUCP implements FunctionsInterface
{
    function runFunction($population, $functionType)
    {
        // return (new Multimodal())->initializingMultimodalFunctions($functionType)->multimodal($population);
        echo 'UCP belum';
    }
}

class Functions
{
    function initializingFunction($functionType)
    {
        $functionTypes = [
            ['function' => 'f1', 'select' => new FunctionF1],
            ['function' => 'f2', 'select' => new FunctionF2],
            ['function' => 'f3', 'select' => new FunctionF3],
            ['function' => 'f4', 'select' => new FunctionF4],
            ['function' => 'f5', 'select' => new FunctionF5],
            ['function' => 'f6', 'select' => new FunctionF6],
            ['function' => 'f7', 'select' => new FunctionF7],
            ['function' => 'f8', 'select' => new FunctionF8],
            ['function' => 'f9', 'select' => new FunctionF9],
            ['function' => 'f10', 'select' => new FunctionF10],
            ['function' => 'f11', 'select' => new FunctionF11],
            ['function' => 'f12', 'select' => new FunctionF12],
            ['function' => 'f13', 'select' => new FunctionF13],
            ['function' => 'ucp', 'select' => new FunctionUCP],
        ];
        $index = array_search($functionType, array_column($functionTypes, 'function'));
        return $functionTypes[$index]['select'];
    }
}