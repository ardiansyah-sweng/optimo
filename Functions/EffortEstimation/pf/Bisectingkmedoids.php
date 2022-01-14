<?php

# read data set
require_once "dataset.php";

/**
 * Bisecting k-Medoids Clustering
 * 
 * @2020 Ardiansyah - ardiansyah@tif.uad.ac.id
 * 
 * The class will return N clusters S = {C1, C2, C3, C4, .., CN} along with Medoid tuple for each cluster.
 * The medoid using random approach
 */

class BisectingKMedoids
{
    private $EXPONENT = 2;

    /**
     * Membaca dataset dari file dataset.php
     * Return berupa array dataset utama
     */
    function readDataset($cacah)
    {
        $dataset = new Dataset();
        $temp = $dataset->dataset();
        // Leave One Out - LOO
        foreach ($temp as $key => $val) {
            if ($cacah != $key) {
                $ret[] = $val;
            }
        }
        return $ret;
    }

    /**
     * Membangkitkan medoid secara random
     * Return array Tupel medoid yang terpilih secara acak
     */
    function randomMedoidTuple($arrDataset)
    {
        $randomIndex = array_rand($arrDataset);
        foreach ($arrDataset as $key => $val) {
            if ($key == $randomIndex) {
                return $val;
            }
        }
    }

    # Calculate distance between tuple and data set
    # Return array sum of all tuple in dataset
    # Calculate variance for each cluster
    # variance = 1/n sum of ||x(i)th data object subsctract v(i)th center of C(i)-th cluster||^2
    # Parameter(s) = array of cluster, center of the cluster
    # Return array variance value for each cluster
    function variance($arrDistance)
    {
        return array_sum($arrDistance) / count($arrDistance);
    }

    /**
     * Sementara khusus hitung distance, nanti direfaktor
     */
    function distance($arrDataset, $arrTupleOFMedoids)
    {
        foreach ($arrDataset as $val) {
            $f1 = POW($val['f1'] - $arrTupleOFMedoids['f1'], $this->EXPONENT);
            $f2 = POW($val['f2'] - $arrTupleOFMedoids['f2'], $this->EXPONENT);
            $f3 = POW($val['f3'] - $arrTupleOFMedoids['f3'], $this->EXPONENT);
            $f4 = POW($val['f4'] - $arrTupleOFMedoids['f4'], $this->EXPONENT);
            $f5 = POW($val['f5'] - $arrTupleOFMedoids['f5'], $this->EXPONENT);
            $f6 = POW($val['f6'] - $arrTupleOFMedoids['f6'], $this->EXPONENT);
            $f7 = POW($val['f7'] - $arrTupleOFMedoids['f7'], $this->EXPONENT);
            $f8 = POW($val['f8'] - $arrTupleOFMedoids['f8'], $this->EXPONENT);
            $retDistance[] = $f1 + $f2 + $f3 + $f4 + $f5 + $f6 + $f7 + $f8;
        }
        return $retDistance;
    }

    # Pairing between Project ID and Distance from medoid tuples
    function pairingDistanceAndProjectID($arrDataset, $arrDistance)
    {
        foreach ($arrDataset as $key => $val) {
            $pairing[$key]['projectID'] = $val['project_id'];
            $pairing[$key]['distance'] = $arrDistance[$key];
        }
        return $pairing;
    }

    # Calculate child variance
    # Return array variance of child clusters
    function childVariance($cluster)
    {
        $cacah = 0;
        foreach ($cluster as $key => $val) {
            foreach ($val as $subkey => $subval) {
                $dataPoint = count($subval);
                foreach ($subval as $subsubval) {
                    $cacah = $cacah + $subsubval['distance'];
                }
                $variance = $cacah / $dataPoint;
                $ret[$key][$subkey] = $variance;
                $cacah = 0;
            }
        }
        return $ret;
    }

    # Compare variance of two child cluster with variance of parent cluster
    # Return boolean [split or stop]
    function compareVariance($arrParentVariance, $arrChildVariance)
    {
        foreach ($arrParentVariance as $key => $val) {
            foreach ($arrChildVariance as $subkey => $subval) {
                if ($key == $subkey) {
                    foreach ($subval as $subsubkey => $subsubval) {
                        if ($val < $subsubval) {
                            $ret[$key][$subsubkey] = 'stop';
                        }
                        if ($val > $subsubval) {
                            $ret[$key][$subsubkey] =  'split';
                        }
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Membuat dua cluster
     * Return dua cluster dengan elemen berisi index
     */
    function createTwoCluster($arrDistanceC1, $arrDistanceC2)
    {
        foreach ($arrDistanceC1 as $key => $val) {
            if ($val < $arrDistanceC2[$key]) {
                $ret['C1'][] = $key;
            } else {
                $ret['C2'][] = $key;
            }
        }
        return $ret;
    }


    /**
     * Program utama Bisecting K-Medoids
     */
    function bisectingKMedoidsClustering($cacah)
    {
        $X[] = $this->readDataset($cacah);
        $NextLevel = array();
        $V = array(); // Inisialisasi V untuk menampung seluruh array cluster
        $V = array_merge($V, $X); // Assign $V untuk menampung dataset utama. count($V) harusnya > 0
        $S = array();

        // echo 'Initial dataset (belum masuk while):<br>';
        // print_r($V);
        // echo '<p>';

        // Ulangi selama $V > 0
        while (count($V) > 0) {
            // Untuk setiap Cluster dalam array $V, lakukan 
            foreach ($V as $val) {
                // Hitung varians tiap Cluster
                // 1. Ambil medoid acak terlebih dahulu dari dataset
                // 2. Hitung varians
                $arrRandomMedoidTupleParent = $this->randomMedoidTuple($val);
                $parentDistance = $this->distance($val, $arrRandomMedoidTupleParent);
                $parentVariance = $this->variance($parentDistance);
                // Split menjadi dua Cluster (C1, C2)
                $arrRandomMedoidTupleC1 = $this->randomMedoidTuple($val);
                $arrRandomMedoidTupleC2 = $this->randomMedoidTuple($val);
                $arrDistanceC1 = $this->distance($val, $arrRandomMedoidTupleC1);
                $arrDistanceC2 = $this->distance($val, $arrRandomMedoidTupleC2);
                $varianceC1 = $this->variance($arrDistanceC1);
                $varianceC2 = $this->variance($arrDistanceC2);
                $twoClusterC1C2 = $this->createTwoCluster($arrDistanceC1, $arrDistanceC2);
                // print_r($twoClusterC1C2);

                // $C1 = $this->pairingDistanceAndProjectID($val, $arrDistanceC1);
                // $C2 = $this->pairingDistanceAndProjectID($val, $arrDistanceC2);
                // Jika MAX($variance C1, $variance C2) < $parent Variance, maka array $NextLevel diisi 2 cluster C1, C2 tersebut
                // Sehingga size count array $V bertambah, dan tetap > 0, sehingga while terus berulang
                if (max($varianceC1, $varianceC2) < $parentVariance) {
                    foreach ($twoClusterC1C2 as $vals) {
                        //echo '<br>' . $key . '<br>';
                        foreach ($vals as $subval) {
                            //echo $subval . '&nbsp;';
                            // print_r($val[$subval]);
                            // echo '<br>';
                            $temp[] = $val[$subval];
                        }
                        //echo '<br>';
                        // echo '<p>';
                        // print_r($temp);
                        $NextLevel[] = $temp;
                        $temp = [];
                    }
                    //echo 'Split<br>';
                } else {
                    // print_r($arrRandomMedoidTupleParent);
                    $S[] = $val;
                    //print_r($S);
                    echo '<p>';
                    $arrMedoidForAllClusters[] = $arrRandomMedoidTupleParent;
                    //echo 'Stop<br>';
                }
            }

            $V = $NextLevel;
            // echo 'Next level<br>';
            // print_r($NextLevel);

            //$V = []; // Mengosongkan array $V supaya count($V) == 0. Sehingga While berhentin
            $NextLevel = [];
        }
        // echo '<p>';
        echo '<h4>Data ke-' . $cacah . '. Final clusters = ' . count($S) . '</h4>';
        echo 'Medoids:<br>';
        print_r($arrMedoidForAllClusters);
        echo '<p>';
        print_r($S);
        echo '<br>';
        // foreach ($S as $key => $val){
        //     print_r($val);
        //     echo '<p>';
        // }
    }
}

$bisecting = new BisectingKMedoids();
for ($i = 0; $i < 120; $i++) {
    $bisecting->bisectingKMedoidsClustering($i);
}
