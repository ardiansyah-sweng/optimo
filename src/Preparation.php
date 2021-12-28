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
                ->initializingLocalParameter($optimizer)
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
            foreach ($variables as $key => $variable) {
                $initializer = new Initializer(
                    $this->optimizerAlgorithms,
                    $this->functionsToOptimized,
                    $variables[0]['ranges'],
                    $parameters[0]['populationSize'],
                    $this->variableType,
                    $this->experimentType,
                    $variable['numOfVariables']
                );
                $optimizer = new Optimizers;
                $optimizer->algorithm = $this->optimizerAlgorithms[0];
                $optimizer->function = $this->functionsToOptimized[$key];
                $optimizer->experimentType = $this->experimentType;
                $optimizer->popsize = $parameters[0]['populationSize'];


                if ($this->experimentType === 'normal') {
                    $res = $optimizer->updating($initializer->generateInitialPopulation());
                }

                if ($this->experimentType === 'evaluation' && $this->variableType === 'seeds') {
                    $pathToResult = (new Paths())->initializePath($optimizer->algorithm);
                    $this->saveToFile($pathToResult, array($optimizer->function));
                    for ($i = 0; $i < 30; $i++) {
                        $res = $optimizer->updating($initializer->generateInitialPopulation()[$i]);

                        $this->saveToFile($pathToResult, array($res));
                    }
                }
            }
        }

        ## One Optimizer for One Function
        if ($this->setupIsOneForOne()) {
            $initializer = new Initializer(
                $this->optimizerAlgorithms,
                $this->functionsToOptimized,
                $variables[0]['ranges'],
                $parameters[0]['populationSize'],
                $this->variableType,
                $this->experimentType,
                $variables[0]['numOfVariables']
            );
            $optimizer = new Optimizers;
            $optimizer->algorithm = $this->optimizerAlgorithms[0];
            $optimizer->function = $this->functionsToOptimized[0];
            $optimizer->experimentType = $this->experimentType;
            $optimizer->popsize = $parameters[0]['populationSize'];

            if ($this->experimentType === 'normal') {
                $res = $optimizer->updating($initializer->generateInitialPopulation());
                print_r($res);
            }

            if ($this->experimentType === 'evaluation' && $this->variableType === 'random') {
                $pathToResult = (new Paths())->initializePath($optimizer->algorithm);
                $this->saveToFile($pathToResult, array($optimizer->function, 'random'));
                for ($i = 0; $i < 30; $i++) {
                    $res = $optimizer->updating($initializer->generateInitialPopulation());

                    $this->saveToFile($pathToResult, array($res));
                }
            }

            if ($this->experimentType === 'evaluation' && $this->variableType === 'seeds') {
                $pathToResult = (new Paths())->initializePath($this->optimizerAlgorithms[0]);
                echo $pathToResult;die;
                $this->saveToFile($pathToResult, array($this->functionsToOptimized[0]));
                for ($i = 0; $i < 30; $i++) {
                    $res = $optimizer->updating($initializer->generateInitialPopulation()[$i]);

                    $this->saveToFile($pathToResult, array($res));
                }
            }
        }
    }
}
