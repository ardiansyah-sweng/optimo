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

        $parameters = [
                    'parameterName' => 'gaParameter',
                    'populationSize' => 30,
                    'cr' => 0.9,
                    'mr' => 0.01
        ];
        $agileVarRanges = [
                ['lowerBound' => 0.91, 'upperBound' => 1],
                ['lowerBound' => 0.89, 'upperBound' => 1],
                ['lowerBound' => 0.96, 'upperBound' => 1],
                ['lowerBound' => 0.85, 'upperBound' => 1],
                ['lowerBound' => 0.91, 'upperBound' => 1],
                ['lowerBound' => 0.96, 'upperBound' => 1],
                ['lowerBound' => 0.90, 'upperBound' => 1],
                ['lowerBound' => 0.98, 'upperBound' => 1],
                ['lowerBound' => 0.98, 'upperBound' => 1],
                ['lowerBound' => 0.96, 'upperBound' => 1],
                ['lowerBound' => 0.95, 'upperBound' => 1],
                ['lowerBound' => 0.97, 'upperBound' => 1],
                ['lowerBound' => 0.98, 'upperBound' => 1]
        ];

        foreach ($agileVarRanges as $range){
            $randomizer = Randomizers::randomVariableValueByRange($range);
            echo $randomizer;
            echo "\n";
        }

    }

}