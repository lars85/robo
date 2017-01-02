<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use Robo\Task\Filesystem\FilesystemStack;

class RemoveMaintenanceFlag extends BaseTask
{
    public function run()
    {
        return $this->runTask(
            (new FilesystemStack())->remove($this->getDeployment()->getMaintenanceFlagFileName()),
            Deployment::PATH_WEB
        );
    }
}