<?php

namespace LarsMalach\Robo\Task\Deployment;

use Robo\Task\Bower\Install;

class BowerInstall extends BaseTask
{
    public function run()
    {
        $bowerTask = new Install();
        return $this->runTask($bowerTask);
    }
}