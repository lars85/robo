<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Factory\DeploymentFactory;
use LarsMalach\Robo\Model\Deployment;

trait loadTasks
{
    /** @var Deployment */
    private $deployment;

    protected function deplInit(string $instanceKey, string $filePath, array $deploymentProperties = []): self
    {
        $deploymentFactory = new DeploymentFactory();
        $this->deployment = $deploymentFactory->createDeployment($instanceKey, $filePath, $deploymentProperties);
        return $this;
    }

    /**
     * @return Deployment
     */
    protected function deplGet()
    {
        return $this->deployment;
    }

    /**
     * @return GitClone
     */
    protected function deplGitClone()
    {
        return $this->task(GitClone::class, $this->deplGet());
    }

    /**
     * @return ComposerInstall
     */
    protected function deplComposerInstall()
    {
        return $this->task(ComposerInstall::class, $this->deplGet());
    }

    /**
     * @return DeployFiles
     */
    protected function deplDeployFiles()
    {
        return $this->task(DeployFiles::class, $this->deplGet());
    }

    /**
     * @return BowerInstall
     */
    protected function deplBowerInstall()
    {
        return $this->task(BowerInstall::class, $this->deplGet());
    }

    /**
     * @return CreateMaintenanceFlag
     */
    protected function deplCreateMaintenanceFlag()
    {
        return $this->task(CreateMaintenanceFlag::class, $this->deplGet());
    }

    /**
     * @return Release
     */
    protected function deplRelease()
    {
        return $this->task(Release::class, $this->deplGet());
    }

    /**
     * @return RemoveMaintenanceFlag
     */
    protected function deplRemoveMaintenanceFlag()
    {
        return $this->task(RemoveMaintenanceFlag::class, $this->deplGet());
    }

    /**
     * @return RemoveLocalTemporaryDirectory
     */
    protected function deplRemoveLocalTemporaryDirectory()
    {
        return $this->task(RemoveLocalTemporaryDirectory::class, $this->deplGet());
    }

    /**
     * @return RemoveOldReleases
     */
    protected function deplRemoveOldReleases()
    {
        return $this->task(RemoveOldReleases::class, $this->deplGet());
    }
}
