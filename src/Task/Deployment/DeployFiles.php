<?php

namespace LarsMalach\Robo\Task\Deployment;

use Robo\Result;
use Robo\Task\Remote\Rsync;

class DeployFiles extends BaseTask
{
    public function run()
    {
        foreach ($this->getDeployment()->getServers() as $server) {
            (new Rsync())
                ->fromPath($this->getDeployment()->getReleasePath() . '/')
                ->toHost($server->getHost())
                ->toUser($server->getUser())
                ->toPath($server->getReleasePath() . '/')
                ->recursive()
                ->compress()
                ->option('links')
                ->stats()
                ->humanReadable()
                ->run();
        }

        return Result::success($this);
    }
}