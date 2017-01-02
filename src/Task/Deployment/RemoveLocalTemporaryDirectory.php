<?php

namespace LarsMalach\Robo\Task\Deployment;

use Robo\Task\Filesystem\DeleteDir;

class RemoveLocalTemporaryDirectory extends BaseTask
{
    public function run()
    {
        return (new DeleteDir($this->getDeployment()->getReleasePath()))
            ->run();
    }
}