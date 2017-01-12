<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use LarsMalach\Robo\Model\Server;
use Robo\Collection\Collection;
use Robo\Contract\CommandInterface;
use Robo\Task\Remote\Ssh;

class ExecOnServers extends BaseTask
{
    /** @var CommandInterface */
    protected $task;

    /** @var Server[] */
    protected $servers;

    /** @var string */
    protected $pathType = Deployment::PATH_RELEASE;

    public function __construct(Deployment $deployment, CommandInterface $task)
    {
        $this->setTask($task);
        parent::__construct($deployment);
    }

    public function getTask()
    {
        return $this->task;
    }

    public function setTask(CommandInterface $task): self
    {
        $this->task = $task;
        return $this;
    }

    public function getPathType(): string
    {
        return $this->pathType;
    }

    public function setPathType(string $pathType): self
    {
        $this->pathType = $pathType;
        return $this;
    }

    public function getServers(): array
    {
        return $this->servers ?: $this->getDeployment()->getServers();
    }

    public function setServers(array $servers): self
    {
        $this->servers = $servers;
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo('Run on servers:');
        $this->printTaskInfo($this->getTask()->getCommand());
        $collection = new Collection();
        $servers = $this->getServers();
        foreach ($servers as $server) {
            $sshTask = (new Ssh($server->getHost(), $server->getUser()))
                ->remoteDir($server->getPath($this->getPathType()))
                ->exec($this->getTask());
            $collection->add($sshTask);
        }
        return $collection->run();
    }
}
