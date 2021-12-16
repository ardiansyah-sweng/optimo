<?php
require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class PreparationTest extends TestCase
{
    function test_getVariableAndParameter()
    {
        $optimizerAlgorithm = ['ucpso'];
        $functionToOptimized = ['agile'];

        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        $result = $prep->getVariableAndParameter();
        print_r($result);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    function test_setupIsAllForAll()
    {
        $optimizerAlgorithm = ['ucpso','cpso'];
        $functionToOptimized = ['agile','cocomo'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        
        $result = $prep->setupIsAllForAll();
        $this->assertTrue($result);

        $optimizerAlgorithm = ['ucpso'];
        $functionToOptimized = ['agile', 'cocomo'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');

        $result = $prep->setupIsAllForAll();
        $this->assertNull($result);

    }

    function test_setupIsAllForOne()
    {
        $optimizerAlgorithm = ['ucpso','cpso'];
        $functionToOptimized = ['agile'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        $result = $prep->setupIsAllForOne();
        ($result);
        $this->assertTrue($result);

        $optimizerAlgorithm = ['ucpso'];
        $functionToOptimized = ['agile'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        $result = $prep->setupIsAllForOne();
        ($result);
        $this->assertNull($result);
    }

    function test_setupIsOneForOne()
    {
        $optimizerAlgorithm = ['ucpso'];
        $functionToOptimized = ['agile'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        $result = $prep->setupIsOneForOne();
        ($result);
        $this->assertTrue($result);

        $optimizerAlgorithm = ['ucpso', 'cpso'];
        $functionToOptimized = ['agile'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        $result = $prep->setupIsOneForOne();
        ($result);
        $this->assertNull($result);
    }

    function test_setup()
    {
        $optimizerAlgorithms = ['pso'];
        $functionToOptimized = ['f1','f2','agile'];

        $prep = new Preparation('evaluation', $optimizerAlgorithms, $functionToOptimized, 'random');
        print_r($prep->setup());die;
    }
}