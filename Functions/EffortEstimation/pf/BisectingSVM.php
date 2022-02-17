<?php

class BisectingSVM
{
    function runBisectingSVM($cValue, $gamma, $klasterSets)
    {
        $bisecting = new BisectingKMedoids();
        $util = new Utils;

        $dataInput = [];
        $jumCluster = 120;
        for ($i = 0; $i < $jumCluster; $i++) {
            
            $rawTestData = $bisecting->getTestData($i);
            $testData = $bisecting->convertingECF($rawTestData);

            foreach ($klasterSets[$i]['clusters'] as $key => $cluster) {
                $rawClusters[] = $bisecting->convertingRaw($cluster, 0);
            }
            foreach ($klasterSets[$i]['clusters'] as $key => $cluster) {
                $rawClustersNN[] = $bisecting->convertingRaw($cluster, 1);
            }

            foreach ($klasterSets[$i]['clusters'] as $cluster) {
                foreach ($cluster as $tupel) {
                    $actualY[] = $tupel['actual'];
                }
            }

            foreach ($rawClusters as $key => $klasters) {
                foreach ($klasters as $tupel) {
                    $labels[] = $key;
                    $data[] = $tupel;
                }
            }

            foreach ($rawClustersNN as $key => $klasters) {
                foreach ($klasters as $tupel) {
                    $labelsNN[] = $key;
                    array_unshift($tupel, 1); // jika mau pakai regression matrix
                    $dataNN[] = $tupel;
                }
            }

            //$dataJson = json_encode($data, 0);
            //$labelJson = json_encode($labels, 0);

            $ret['dataTrain'] = $data;
            $ret['dataLabel'] = $labels;
            $ret['gamma'] = $gamma;
            $ret['dataTest'] = $testData;
            $ret['cVal'] = $cValue;

            $kernel = "Radial Basis";

            $url = 'http://localhost:8000/count';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ret, 0));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response  = curl_exec($ch);
            curl_close($ch);
            $result = array_values(json_decode($response, true))[0];
            $results = json_decode($response, true);

            //check predicted cluster contains testData or not
            $predictedClusterNo = $result[$kernel];
            $rawClusters = [];

            $medoid = $klasterSets[$i]['medoids'][$predictedClusterNo];
            $dataInputs[] = [
                $rawTestData['actual'],
                $medoid['actualPF'],
                $rawTestData['size']
            ];

            // $transposedDataNN = $util->transpose($dataNN);
            // $weights = $util->matrixMultiplication($actualY, $transposedDataNN);

            // foreach ($dataInput as $input) {
            //     $estimatedEffort = $weights[0] + ($weights[1] * $input[2]) + ($weights[2] * $input[1]);
            //     $ae[$i][$input[0]] = abs($estimatedEffort - $rawTestData['actual']);
            // }

            //$dataNN = [];
            $rawClustersNN = [];
            $labelsNN = [];
        }

        $dataNormalization = new MinMaxScaler;
        $dataNormalization->dataset = $dataInputs;
        $normalDataInputs = $dataNormalization->normalization();

        $sgdOptimizer = new StochasticGD;
        $sgdOptimizer->normalizedDataset = $normalDataInputs;
        $sgdOptimizer->dataProcessing();
    }
}
