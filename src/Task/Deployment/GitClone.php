<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use Robo\Task\Base\Exec;

class GitClone extends BaseTask
{
    public function run()
    {
        $gitTask = (new Exec('git clone'))
            ->option('branch', $this->getDeployment()->getRevisionName())
            ->option('single-branch')
            ->option('depth', 1)
            ->arg($this->getDeployment()->getRepository())
            ->arg($this->getDeployment()->getReleaseName());

        return $this->runTask($gitTask, Deployment::PATH_ROOT);
    }
}