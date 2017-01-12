<?php

namespace LarsMalach\Robo\Task\Deployment;

use Robo\Task\Npm\Install;

class NpmInstall extends BaseTask
{
    public function run()
    {
        $npmTask = new Install();

        $npmDir = $this->getDeployment()->getProperty('npm.dir');
        if (!empty($npmDir)) {
            $npmTask->dir($npmDir);
        }

        return $this->runTask($npmTask);
    }
}