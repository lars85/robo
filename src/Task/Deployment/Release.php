<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use Robo\Task\Base\ExecStack;

class Release extends BaseTask
{
    public function run()
    {
        $execTasks = (new ExecStack())
            ->exec('rm -rf htdocs')
            ->exec('ln -s ' . rtrim('current/' . $this->getDeployment()->getWebDirectory(), '/') . ' htdocs')
            ->exec('rm -rf previous')
            ->exec('if [ -e current ]; then mv current previous; fi')
            ->exec('ln -s ' . $this->getDeployment()->getReleaseName() . ' current');

        return $this->runTask($execTasks, Deployment::PATH_ROOT);
    }
}