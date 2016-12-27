<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Factory\DeploymentFactory;
use LarsMalach\Robo\Model\Deployment;
use Robo\Task\Base\Exec;
use Robo\Task\Base\ExecStack;
use Robo\Task\BaseTask;
use Robo\Task\Bower\Install;
use Robo\Task\Filesystem\DeleteDir;
use Robo\Task\Remote\Rsync;
use Robo\Task\Remote\Ssh;

class Base extends BaseTask
{
    /** @var Deployment */
    protected $deployment;

    public function __construct(string $instanceKey, string $filePath = '', array $deploymentProperties = [])
    {
        $deploymentFactory = new DeploymentFactory();
        $this->deployment = $deploymentFactory->createDeployment($instanceKey, $filePath, $deploymentProperties);
    }

    public function run()
    {
        // do nothing
    }

    public function getDeployment()
    {
        return $this->deployment;
    }

    public function gitClone(bool $execLocal = false): self
    {
        $this->printTaskInfo(__FUNCTION__);

        $gitTask = (new Exec('git clone'))
            ->option('branch', $this->getDeployment()->getRevisionName())
            ->option('single-branch')
            ->option('depth', 1)
            ->option(null, $this->getDeployment()->getRepository());

        if ($execLocal) {
            $gitTask
                ->option(null, $this->getDeployment()->getLocalTemporaryPath())
                ->run();
        } else {
            foreach ($this->getDeployment()->getServers() as $server) {
                $gitTaskClone = clone $gitTask;
                $gitTaskClone->option(null, $server->getReleasePath($this->getDeployment()));
                (new Ssh($server->getHost(), $server->getUser()))
                    ->exec($gitTaskClone)
                    ->run();
            }
        }

        return $this;
    }

    public function composerInstall(bool $execLocal = false): self
    {
        $this->printTaskInfo(__FUNCTION__);

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

        if ($execLocal) {
            $composerTask
                ->dir($this->getDeployment()->getLocalTemporaryPath())
                ->run();
        } else {
            foreach ($this->getDeployment()->getServers() as $server) {
                (new Ssh($server->getHost(), $server->getUser()))
                    ->remoteDir($server->getReleasePath($this->getDeployment()))
                    ->exec($composerTask)
                    ->run();
            }
        }

        return $this;
    }

    public function deployFiles(): self
    {
        $this->printTaskInfo(__FUNCTION__);

        foreach ($this->getDeployment()->getServers() as $server) {
            (new Rsync())
                ->fromPath($this->getDeployment()->getLocalTemporaryPath() . '/')
                ->toHost($server->getHost())
                ->toUser($server->getUser())
                ->toPath($server->getReleasePath($this->getDeployment()))
                ->recursive()
                ->compress()
                ->option('links')
                ->stats()
                ->humanReadable()
                ->run();
        }

        return $this;
    }

    public function bowerInstall(bool $execLocal = false): self
    {
        $this->printTaskInfo(__FUNCTION__);

        $bowerTask = new Install();

        if ($execLocal) {
            $bowerTask
                ->dir($this->getDeployment()->getLocalTemporaryPath())
                ->run();
        } else {
            foreach ($this->getDeployment()->getServers() as $server) {
                (new Ssh($server->getHost(), $server->getUser()))
                    ->remoteDir($server->getReleasePath($this->getDeployment()))
                    ->exec($bowerTask)
                    ->run();
            }
        }

        return $this;
    }

    public function release(bool $execLocal = false): self
    {
        $this->printTaskInfo(__FUNCTION__);

        $execTasks = (new ExecStack())
            ->exec('rm -rf htdocs')
            ->exec('ln -s current htdocs')
            ->exec('rm -rf previous')
            ->exec('if [ -e current ]; then mv current previous; fi')
            ->exec('ln -s release_' . $this->getDeployment()->getReleaseName() . '/' . $this->getDeployment()->getWebDirectory() . ' current');

        if ($execLocal) {
            $execTasks
                ->dir($this->getDeployment()->getLocalTemporaryPath())
                ->run();

        } else {
            foreach ($this->getDeployment()->getServers() as $server) {
                (new Ssh($server->getHost(), $server->getUser()))
                    ->remoteDir($server->getPath())
                    ->exec($execTasks)
                    ->run();
            }
        }

        return $this;
    }

    public function removeLocalTemporaryDirectory(): self
    {
        $this->printTaskInfo(__FUNCTION__);

        (new DeleteDir($this->getDeployment()->getLocalTemporaryPath()))
            ->run();

        return $this;
    }

    public function removeOldReleases(bool $execLocal = false): self
    {
        $this->printTaskInfo(__FUNCTION__);

        $execTasks = (new ExecStack())
            ->exec(
                'ls -t --ignore="release_cache" -1 . | ' .
                'grep "^release_" | ' .
                'tail -n +' . ($this->getDeployment()->getKeepReleases() + 1) . ' | ' .
                'xargs -I {} rm -rf ./{}'
            );

        if ($execLocal) {
            $execTasks
                ->dir($this->getDeployment()->getLocalTemporaryPath())
                ->run();

        } else {
            foreach ($this->getDeployment()->getServers() as $server) {
                (new Ssh($server->getHost(), $server->getUser()))
                    ->remoteDir($server->getPath())
                    ->exec($execTasks)
                    ->run();
            }
        }

        return $this;
    }
}
