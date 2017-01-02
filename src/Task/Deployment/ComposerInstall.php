<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use Robo\Task\Base\Exec;

class ComposerInstall extends BaseTask
{
    public function run()
    {
        $composerTask = (new Exec('composer install'))
            ->option('optimize-autoloader')
            ->option('ignore-platform-reqs');
        if ($this->getDeployment()->getContext() === Deployment::CONTEXT_PRODUCTION) {
            $composerTask
                ->option('prefer-dist')
                ->option('no-dev');
        } else {
            $composerTask
                ->option('prefer-source');
        }

        return $this->runTask($composerTask);
    }
}