<?php

class Preparation
{
    private $experimentType;
    private $optimizerAlgorithms;
    private $functionsToOptimized;
    private $variableType;

    function __construct(string $experimentType, array $optimizerAlgorithms, array $functionsToOptimized, string $variableType)
    {
        $this->experimentType = $experimentType;
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
        $this->variableType = $variableType;
    }

    function getVariableAndParameter(): array
    {
        foreach ($this->functionsToOptimized as $function) {
            $variables[] = (new Variables())
                ->initializeVariableFactory($function)
                ->getVariables('');
        }
        foreach ($this->optimizerAlgorithms as $optimizer) {
            $parameters[] = (new LocalParameterFactory())
                ->initializingLocalParameter($optimizer, $variables[0]['numOfVariables'])
                ->getLocalParameter();
        }
        return [
            'parameter' => $parameters,
            'variable' => $variables
        ];
    }

    function setupIsAllForAll()
    {
        if (count($this->optimizerAlgorithms) > 1 && count($this->functionsToOptimized) > 1) {
            return true;
        }
    }

    function setupIsAllForOne()
    {
        if (count($this->optimizerAlgorithms) > 1 && count($this->functionsToOptimized) === 1) {
            return true;
        }
    }

    function setupIsOneForAll()
    {
        if (count($this->optimizerAlgorithms) === 1 && count($this->functionsToOptimized) > 1) {
            return true;
        }
    }

    function setupIsOneForOne()
    {
        if (count($this->optimizerAlgorithms) === 1 && count($this->functionsToOptimized) === 1) {
            return true;
        }
    }

    function saveToFile($path, $data)
    {
        $fp = fopen($path, 'a');
        fputcsv($fp, $data);
        fclose($fp);
    }

    function setup()
    {
        $parameters = $this->getVariableAndParameter()['parameter'];
        $variables = $this->getVariableAndParameter()['variable'];

        # All Optimizer for All Functions
        if ($this->setupIsAllForAll()) {
            foreach ($parameters as $parameter) {
                foreach ($variables as $variable) {
                    $optimizer = new Initializer(
                        $this->optimizerAlgorithms,
                        $this->functionsToOptimized,
                        $variable['ranges'],
                        $parameters[0]['populationSize'],
                        $this->variableType,
                        $this->experimentType,
                        $variables['numOfVariables']
                    );
                }
            }
        }

        ## All Optimizer for One Function
        if ($this->setupIsAllForOne()) {
            foreach ($parameters as $parameter) {
                $optimizer = new Initializer(
                    $this->optimizerAlgorithms,
                    $this->functionsToOptimized,
                    $variables[0]['ranges'],
                    $parameter['populationSize'],
                    $this->variableType,
                    $this->experimentType,
                    $variables['numOfVariables']
                );
                $ret[] = $optimizer->generateInitialPopulation();
            }
            return $ret;
        }

        ## One Optimizer for All Functions
        if ($this->setupIsOneForAll()) {

            if ($this->optimizerAlgorithms[0] === 'komodo') {
                $populationSize = $parameters[0]['n1'];
            } else {
                $populationSize = $parameters[0]['populationSize'];
            }

            foreach ($variables as $key => $variable) {
                $initializer = new Initializer(
                    $this->optimizerAlgorithms,
                    $this->functionsToOptimized,
                    $variables[0]['ranges'],
                    $populationSize,
                    $this->variableType,
                    $this->experimentType,
                    $variable['numOfVariables']
                );
                $optimizer = new Optimizers;
                $optimizer->algorithm = $this->optimizerAlgorithms[0];
                $optimizer->experimentType = $this->experimentType;
                $optimizer->popsize = $populationSize;
                $optimizer->parameters = $parameters[0];
                $optimizer->variableRanges = $variables[0]['ranges'];

                $pathToResult = (new Paths())->initializePath($optimizer->algorithm);

                if ($this->experimentType === 'normal') {
                    $res = $optimizer->updating($initializer->generateInitialPopulation(), '');
                }

                if ($this->experimentType === 'evaluation' && $this->variableType === 'seeds') {
                    foreach ($this->functionsToOptimized as $function) {
                        $optimizer->function = $function;
                        $this->saveToFile($pathToResult, array($function));
                        for ($i = 0; $i < 30; $i++) {
                            $res = $optimizer->updating($initializer->generateInitialPopulation()[$i], '');
                            $this->saveToFile($pathToResult, array($res));
                        }
                    }
                }
            }
        }

        ## One Optimizer for One Function
        if ($this->setupIsOneForOne()) {
            if ($this->optimizerAlgorithms[0] === 'komodo') {
                $populationSize = $parameters[0]['n1'];
            } else {
                $populationSize = $parameters[0]['populationSize'];
            }

            $initializer = new Initializer(
                $this->optimizerAlgorithms,
                $this->functionsToOptimized,
                $variables[0]['ranges'],
                $populationSize,
                $this->variableType,
                $this->experimentType,
                $variables[0]['numOfVariables']
            );

            $optimizer = new Optimizers;
            $optimizer->algorithm = $this->optimizerAlgorithms[0];
            $optimizer->function = $this->functionsToOptimized[0];
            $optimizer->experimentType = $this->experimentType;
            $optimizer->popsize = $populationSize;
            $optimizer->parameters = $parameters[0];
            $optimizer->variableRanges = $variables[0]['ranges'];

            $pathToResult = (new Paths())->initializePath($optimizer->algorithm);

            $data = (new DataProcessor())->initializeDataprocessor('seeds', 50);
            $testDataset = $data->processingData('Dataset\EffortEstimation\Public\ucp_silhavy.txt');

            if ($this->experimentType === 'normal') {
                if ($optimizer->function === 'ucp') {
                    foreach ($testDataset as $testData) {
                        $ae = $optimizer->updating($initializer->generateInitialPopulation(), $testData);
                        if (count($ae) === 1) {
                            $absoluteErrors[] = $ae[0]['fitness'];
                        } else {
                            $absoluteErrors[] = $ae['fitness'];
                        }
                    }
                    $res = array_sum($absoluteErrors) / count($absoluteErrors);
                } else {
                    $res = $optimizer->updating($initializer->generateInitialPopulation(), '');
                }
                print_r($res);
            }

            if ($this->experimentType === 'evaluation' && $this->variableType === 'random') {
                $this->saveToFile($pathToResult, array($optimizer->function, 'random'));

                for ($i = 0; $i < 30; $i++) {
                    if ($optimizer->function === 'ucp') {
                        foreach ($testDataset as $key => $testData) {
                            $absoluteErrors[] = $optimizer->updating($initializer->generateInitialPopulation(), $testData);
                        }
                        $res = array_sum($absoluteErrors) / count($absoluteErrors);
                    } else {
                        $res = $optimizer->updating($initializer->generateInitialPopulation(), '');
                    }

                    $this->saveToFile($pathToResult, array($res));
                }
            }

            if ($this->experimentType === 'evaluation' && $this->variableType === 'seeds') {
                $optimizer->variableType = 'seeds';
                $this->saveToFile($pathToResult, array($this->functionsToOptimized[0], 'seeds', $populationSize));

                for ($i = 0; $i < 30; $i++) {
                    if ($optimizer->function === 'ucp') {
                        foreach ($testDataset as $testData) {
                            if ($optimizer->algorithm === 'wolf') {
                                $absoluteErrors[] = $optimizer->updating($initializer->generateInitialPopulation(), $testData);
                            } else {
                                $absoluteErrors[] = $optimizer->updating($initializer->generateInitialPopulation()[$i], $testData);
                            }
                        }
                        $res = array_sum($absoluteErrors) / count($absoluteErrors);
                    } else if ($optimizer->algorithm === 'wolf') {
                        $res = $optimizer->updating($initializer->generateInitialPopulation(), '');
                    } else {
                        if ($this->functionsToOptimized[0] === 'ucpSVMZhou') {
                            $klasterSets = (new BisectingKMedoidsGenerator())->clusterGenerator();
                        }
                        $optimizer->klasterSets = $klasterSets;
                        $res = $optimizer->updating($initializer->generateInitialPopulation()[$i], '');
                    }
                    $this->saveToFile($pathToResult, array($res['fitness'], $res['individu'][0], $res['individu'][1]) );
                }
                $klasterSets = [];
            }

            if ($this->experimentType === 'convergence') {

                //generate new clusters from bisecting kMedoids
                if ($this->functionsToOptimized[0] === 'ucpSVMZhou') {
                    $cacah = 0;
                    $bisecting = new BisectingKMedoids();

                    for ($j = 0; $j < 120; $j++) {
                        $klasters = $bisecting->bisectingKMedoidsClustering($j);
                        while ($cacah < 1) {
                            if (count($klasters['clusters']) < 2) {
                                $klasters = $bisecting->bisectingKMedoidsClustering($j);
                                $cacah = 0;
                            } else {
                                break;
                            }
                        }
                        $klasterSets[] = $klasters;
                    }
                }

                $this->saveToFile($pathToResult, array($this->functionsToOptimized[0], 'convergence', 'popSize'));

                $optimizer->klasterSets = $klasterSets;
                for ($maxIter = 2; $maxIter <= 10; $maxIter += 2) {
                    $optimizer->maxIter = $maxIter;
                    for ($i = 0; $i < 3; $i++) {
                        $res[] = $optimizer->updating($initializer->generateInitialPopulation(), '');
                    }

                    $result = array_sum($res) / count($res);
                    $res = [];
                    $this->saveToFile($pathToResult, array($maxIter, $result, $optimizer->popsize));
                }
            }
        }
    }
}
