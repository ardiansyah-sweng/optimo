<?php
require 'vendor/autoload.php';

//use Functions\TestFunctions\UnimodalFunctionsFactory as Unimodal;
//use Functions\TestFunctions\MultimodalFunctionsFactory as Multimodal;

interface FunctionsInterface
{
    function runFunction(array $individu, $functionType);
}

class FunctionF1 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($individu);
    }
}

class FunctionF2 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($individu);
    }
}

class FunctionF3 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($individu);
    }
}

class FunctionF4 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($individu);
    }
}

class FunctionF5 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($individu);
    }
}

class FunctionF6 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($individu);
    }
}

class FunctionF7 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new UnimodalFunctionsFactory())->initializingUnimodalFunctions($functionType)->unimodal($individu);
    }
}

class FunctionF8 implements FunctionsInterface
{
    function runFunction($individu, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($individu);
    }
}

class FunctionF9 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($individu);
    }
}

class FunctionF10 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($individu);
    }
}

class FunctionF11 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($individu);
    }
}

class FunctionF12 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($individu);
    }
}

class FunctionF13 implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        return (new MultimodalFunctionsFactory())->initializingMultimodalFunctions($functionType)->multimodal($individu);
    }
}

class FunctionUCP implements FunctionsInterface
{
    function __construct($testData)
    {
        $this->testData = $testData;
    }

    function runFunction(array $individu, $functionType)
    {
        $ucp = new UseCasePoints(20);
        return $ucp->estimating($individu, $this->testData);
    }
}

class FunctionUCPSVM implements FunctionsInterface
{
    function runFunction(array $individu, $functionType)
    {
        echo 'ucpSVM';die;
    }
}

class Functions
{
    function initializingFunction($functionType, $testData)
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
            ['function' => 'ucp', 'select' => new FunctionUCP($testData)],
            ['function' => 'ucpSVM', 'select' => new FunctionUCPSVM]
        ];
        $index = array_search($functionType, array_column($functionTypes, 'function'));
        return $functionTypes[$index]['select'];
    }
}