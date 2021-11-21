<?php
require 'vendor/autoload.php';

$optimizerAlgorithm = ['pso'];
$optimizerAlgorithms = ['pso', 'cpso', 'ga', 'rao'];
$functionToOptimized = ['f1'];
$functionsToOptimized = ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13', 'ucp', 'cocomo', 'agile'];

/**
 * Normal: just seek optimized value
 * Convergence: seek the stabil convergence
 * Evaluation: repeat solution N times
 */
$experimentType = ['evaluation','convergence','normal'];

/**
 * 
 */
$variableType = ['random','seeds'];

$experiment = new Preparation($experimentType[1], $optimizerAlgorithm, $functionToOptimized, $variableType[1]);
$result = $experiment->setup();

