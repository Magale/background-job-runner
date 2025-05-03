<?php

namespace Jobs;

class SampleJob
{
    public function execute($a, $b)
    {
        echo "Executing SampleJob with parameters: $a and $b\n";
    }
}
