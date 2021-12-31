<?php

class SPBest
{
    function getCPBestIndex($pbests): array
    {
        $numOfCPbest = 2;
        for ($i = 0; $i < $numOfCPbest; $i++) {
            $cpbestIndex[] = array_rand($pbests);
        }
        return $cpbestIndex;
    }

    function getSPBest($pbests)
    {
        $counter = 0;
        $cpbests = $this->getCPBestIndex($pbests);
        while ($counter < 1000) {
            if ($cpbests[0] == $cpbests[1]) {
                $cpbests = $this->getCPBestIndex($pbests);
                $counter = 0;
            } else {
                break;
            }
        }

        if ($pbests[$cpbests[0]]['pBest']['fitness'] < $pbests[$cpbests[1]]['pBest']['fitness']) {
            $cpbest = $pbests[$cpbests[0]];
        }
        if ($pbests[$cpbests[0]]['pBest']['fitness'] > $pbests[$cpbests[1]]['pBest']['fitness']) {
            $cpbest = $pbests[$cpbests[1]];
        }
        if ($pbests[$cpbests[0]]['pBest']['fitness'] == $pbests[$cpbests[1]]['pBest']['fitness']) {
            $cpbest = $pbests[$cpbests[0]];
        }

        foreach ($pbests as $key => $pbest){
            if ($pbest['pBest']['fitness'] > $cpbest['pBest']['fitness']){
                $pbests[$key]['pBest'] = $cpbest['pBest'];
            }
        }

        return $pbests;
    }
}
