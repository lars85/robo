<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use Robo\Task\Filesystem\FilesystemStack;

class CreateMaintenanceFlag extends BaseTask
{
    public function run()
    {
        return $this->runTask(
            (new FilesystemStack())->touch($this->getDeployment()->getMaintenanceFlagFileName()),
            Deployment::PATH_WEB
        );
    }
}