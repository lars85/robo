<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use Robo\Task\Base\ExecStack;

class RemoveOldReleases extends BaseTask
{
    /** @var string */
    protected $pattern = '^release_';

    /** @var string */
    protected $ignore = 'release_cache';

    public function getPattern(): string
    {
        return $this->getDeployment()->getProperty('removeOldReleases.regEx') ?: $this->pattern;
    }

    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function getIgnore(): string
    {
        return $this->getDeployment()->getProperty('removeOldReleases.ignore') ?: $this->ignore;
    }

    public function setIgnore(string $ignore): self
    {
        $this->ignore = $ignore;
        return $this;
    }

    public function run()
    {
        $execTasks = (new ExecStack())
            ->exec(
                'ls -t --ignore="' . $this->getIgnore() . '" -1 . | ' .
                'grep "' . $this->getPattern() . '" | ' .
                'tail -n +' . ($this->getDeployment()->getKeepReleases() + 1) . ' | ' .
                'xargs -I {} rm -rf ./{}'
            );

        return $this->runTask($execTasks, Deployment::PATH_ROOT);
    }
}