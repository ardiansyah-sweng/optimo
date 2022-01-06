<?php

class HuntingOperator
{
    function es($maxIteration)
    {
        return 2 * rand(-1, 1) * (1 - (1 / $maxIteration));
    }
}
