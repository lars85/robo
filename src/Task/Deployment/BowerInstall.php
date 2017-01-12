<?php

namespace LarsMalach\Robo\Task\Deployment;

use Robo\Task\Bower\Install;

class BowerInstall extends BaseTask
{
    public function run()
    {
        $bowerTask = new Install();

        $bowerDir = $this->getDeployment()->getProperty('bower.dir');
        if (!empty($bowerDir)) {
            $bowerTask->dir($bowerDir);
        }

        return $this->runTask($bowerTask);
    }
}