<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use Robo\Contract\CommandInterface;
use Robo\Result;
use Robo\Task\Remote\Ssh;

abstract class BaseTask extends \Robo\Task\BaseTask
{
    /** @var bool */
    protected $execLocal = false;

    /** @var Deployment */
    protected $deployment;

    public function __construct(Deployment $deployment)
    {
        $this->deployment = $deployment;
    }

    public function getDeployment(): Deployment
    {
        return $this->deployment;
    }

    public function setExecLocal(bool $execLocal = true)
    {
        $this->execLocal = $execLocal;
    }

    public function isExecLocal(): bool
    {
        return $this->execLocal;
    }

    protected function runTask(CommandInterface $task, string $pathType = Deployment::PATH_RELEASE) {
        $this->printTaskInfo($task->getCommand());
        if ($this->isExecLocal()) {
            return $this->execLocal($task, $pathType);
        } else {
            return $this->execRemote($task, $pathType);
        }
    }

    protected function execLocal(CommandInterface $task, string $pathType = Deployment::PATH_RELEASE)
    {
        return $task
            ->dir($this->getDeployment()->getLocalPath($pathType))
            ->run();
    }

    protected function execRemote(CommandInterface $task, string $pathType = Deployment::PATH_RELEASE)
    {
        foreach ($this->getDeployment()->getServers() as $server) {
            (new Ssh($server->getHost(), $server->getUser()))
                ->remoteDir($server->getPath($pathType))
                ->exec($task)
                ->run();
        }
        return Result::success($this);
    }
}