<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use LarsMalach\Robo\Model\Server;
use Robo\Collection\Collection;
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
        $this->printTaskInfo('Run on servers:');
        $this->printTaskInfo($task->getCommand());
        $collection = new Collection();
        foreach ($this->getDeployment()->getServers() as $server) {
            $sshTask = (new Ssh($server->getHost(), $server->getUser()))
                ->remoteDir($server->getPath($pathType))
                ->exec($task);
            $collection->add($sshTask);
        }
        return $collection->run();
    }

    protected function runTaskOnServer(
        Server $server,
        CommandInterface $task,
        string $pathType = Deployment::PATH_RELEASE
    ): Result {
        $this->printTaskInfo('Run on server "' . $server->getName() . '":');
        $this->printTaskInfo($task->getCommand());
        return (new Ssh($server->getHost(), $server->getUser()))
            ->remoteDir($server->getPath($pathType))
            ->exec($task)
            ->run();
    }
}