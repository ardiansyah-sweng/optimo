<?php

class BisectingSVM
{
    function runBisectingSVM($cValue, $gamma, $klasterSets)
    {
        $bisecting = new BisectingKMedoids();
        $util = new Utils;
  
        $dataInput = [];
        $jumCluster = 5;
        for ($i = 0; $i < $jumCluster; $i++) {
            // echo $i.' '.count($klasterSets[$i]['clusters']);
            // echo "\n";
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

            $rawClusters = [];

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
            // echo $cValue.' '. $gamma."\n";
            // var_dump($response);
            // echo "\n";

            $kernel = 'Linear';
            foreach ($klasterSets[$i]['medoids'] as $key => $medoid) {
                foreach ($result as $key1 => $val) {
                    if ($key === $val && $key1 === $kernel) {
                        $dataInput[] = [
                            $key1,
                            $medoid['actualPF'],
                            $rawTestData['size']
                        ];
                        $rets['dataInput'] = $dataInput;
                    }
                    $rets['dataTrain'] = $dataNN;
                }
            }

            $transposedDataNN = $util->transpose($dataNN);
            $weights = $util->matrixMultiplication($actualY, $transposedDataNN);

            foreach ($dataInput as $input) {
                $estimatedEffort = $weights[0] + ($weights[1] * $input[2]) + ($weights[2] * $input[1]);
                $ae[$i][$input[0]] = abs($estimatedEffort - $rawTestData['actual']);
            }
            
            //$dataNN = [];
            $rawClustersNN = [];
            $labelsNN = [];
        }
        //echo "\n";
        $labels = [];
        $data = [];
        $dataInput = [];
        $rawClustersNN = [];
        $rets = [];
        $dataNN = [];
        $labelsNN = [];
        $actualY = [];

        foreach ($ae as $key => $error) {
            // $rb[] = $error['Radial Basis'];
            // $poly[] = $error['Polynomial'];
            // $sig[] = $error['Sigmoid'];
            $linear[] = $error[$kernel];
        }

        // $maeRadial = array_sum($rb) / $jumCluster;
        // $maePolynomial = array_sum($poly) / $jumCluster;
        // $maeSigmoid = array_sum($sig) / $jumCluster;
        $maeLinear = array_sum($linear) / $jumCluster;

        // $ret = [
        //     'fitnessRadial' => $maeRadial,
        //     'fitnessPolynomial' => $maePolynomial,
        //     'fitnessSigmoid' => $maeSigmoid,
        //     'fitnessLinear' => $maeLinear 
        // ];

        return $maeLinear;
    }
}
