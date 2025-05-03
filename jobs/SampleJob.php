// File: Jobs/SampleJob.php

namespace Jobs;

class SampleJob
{
    public function execute($param1, $param2)
    {
        echo "Executing SampleJob with: $param1 and $param2\n";
    }
}
