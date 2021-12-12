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
        $optimizerAlgorithm = ['ucpso'];
        $functionToOptimized = ['agile'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        $result = $prep->setupIsAllForAll();
        ($result);
        $this->assertNull($result);
    }
}