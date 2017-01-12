<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use LarsMalach\Robo\Model\Server;
use Robo\Contract\CommandInterface;
use Robo\Result;

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

    protected function runTask(CommandInterface $task, string $pathType = Deployment::PATH_RELEASE): Result
    {
        if ($this->isExecLocal()) {
            return $this->runTaskLocal($task, $pathType);
        } else {
            return $this->runTaskOnServers($task, $pathType);
        }
    }

    protected function runTaskLocal(CommandInterface $task, string $pathType = Deployment::PATH_RELEASE): Result
    {
        $this->printTaskInfo('Run local:');
        $this->printTaskInfo($task->getCommand());
        return $task
            ->dir($this->getDeployment()->getLocalPath($pathType))
            ->run();
    }

    protected function runTaskOnServers(CommandInterface $task, string $pathType = Deployment::PATH_RELEASE): Result
    {
        return (new ExecOnServers($this->getDeployment(), $task))
            ->setPathType($pathType)
            ->run();
    }

    protected function runTaskOnServer(
        Server $server,
        CommandInterface $task,
        string $pathType = Deployment::PATH_RELEASE
    ): Result {
        return (new ExecOnServers($this->getDeployment(), $task))
            ->setPathType($pathType)
            ->setServers([$server])
            ->run();
    }
}