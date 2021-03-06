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
        echo "\n";
        print_r($result);
        echo "\n";
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    function test_setupIsAllForAll()
    {
        $optimizerAlgorithm = ['ucpso', 'cpso'];
        $functionToOptimized = ['agile', 'cocomo'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'seeds');

        $result = $prep->setupIsAllForAll();
        $this->assertTrue($result);
    }

    function test_setupIsAllForOne()
    {
        $optimizerAlgorithm = ['ucpso', 'cpso'];
        $functionToOptimized = ['agile'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'random');
        $result = $prep->setupIsAllForOne();
        ($result);
        $this->assertTrue($result);
    }

    function test_setupIsOneForOne()
    {
        $optimizerAlgorithm = ['komodo'];
        $functionToOptimized = ['agile'];
        $prep = new Preparation('convergence', $optimizerAlgorithm, $functionToOptimized, 'seeds');
        $result = $prep->setupIsOneForOne();
        ($result);
        $this->assertTrue($result);
    }

    function test_setup_oneOptimizerOneFunction()
    {
        $optimizerAlgorithms = ['komodo'];
        $functionsToOptimized = ['ucpSVMZhou'];
        $prep = new Preparation('evaluation', $optimizerAlgorithms, $functionsToOptimized, 'seeds');
        $prep->setup();
        die;
    }

    function test_normal()
    {
        $saveFile = new FileSaver;
        for ($i = 0; $i < 30; $i++) {
            $klasterSet = (new BisectingKMedoidsGenerator())->clusterGenerator();
            $normal = (new BisectingSVM())->runBisectingSVM(1, 1, $klasterSet);
            //$saveFile->saveToFile('results\normalSVM.txt', array('======='));
            $saveFile->saveToFile('results\normalSVM.txt', array($normal));
        }
    }

    function test_setup_oneOptimizerAllFunctions()
    {
        $optimizerAlgorithms = ['lion'];
        $functionsToOptimized = ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13'];

        $prep = new Preparation('evaluation', $optimizerAlgorithms, $functionsToOptimized, 'seeds');
        $prep->setup();
        die;
    }

    function test_setup_allOptimizerOneFunction()
    {
        $optimizerAlgorithms = ['ga', 'pso', 'rao'];
        $functionsToOptimized = ['ucp'];

        $prep = new Preparation('evaluation', $optimizerAlgorithms, $functionsToOptimized, 'random');
        print_r($prep->setup());
    }

    function test_setup_allOptimizerAllFunctions()
    {
        $optimizerAlgorithms = ['ga', 'pso', 'rao'];
        $functionsToOptimized = ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13', 'agile', 'cocomo'];

        $prep = new Preparation('evaluation', $optimizerAlgorithms, $functionsToOptimized, 'seeds');
        print_r($prep->setup());
        die;
    }
}
