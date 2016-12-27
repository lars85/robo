<?php

namespace LarsMalach\Robo\Task\Deployment;

trait loadTasks
{
    /**
     * @param string $instanceKey
     * @param string $filePath
     * @param array $deploymentProperties
     * @return Base
     */
    protected function taskDeployment(string $instanceKey, string $filePath, array $deploymentProperties = [])
    {
        return $this->task(Base::class, $instanceKey, $filePath, $deploymentProperties);
    }
}
