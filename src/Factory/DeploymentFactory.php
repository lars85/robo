<?php

namespace LarsMalach\Robo\Factory;

use LarsMalach\Robo\Model\Deployment;
use Symfony\Component\Yaml\Yaml;
use Robo\Exception\TaskException;

class DeploymentFactory
{
    public function createDeployment(
        string $instanceKey,
        string $filePath = '',
        array $deploymentProperties = []
    ): Deployment {
        $instances = [];
        if (!empty($filePath)) {
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            switch ($fileExtension) {
                case 'yaml':
                    $instances = Yaml::parse($filePath);
                    break;
                case 'json':
                    $instances = json_decode(file_get_contents($filePath), true);
                    break;
                default:
                    throw new TaskException($this, 'Unknown file extension "' . $fileExtension . '"');
            }
        }

        $instances[$instanceKey] = array_replace_recursive(
            !empty($instances[$instanceKey]) ? $instances[$instanceKey] : [],
            array_filter($deploymentProperties)
        );
        return $this->createDeploymentFromArray($instanceKey, $instances);
    }

    public function createDeploymentFromArray(string $instanceKey, array $instances): Deployment
    {
        if (empty($instances[$instanceKey])) {
            throw new TaskException($this, 'Deployment "' . $instanceKey . '" not found in servers.json');
        }

        // Resolve super types
        if (!empty($instances[$instanceKey]['abstract'])) {
            throw new TaskException($this, 'Can not get an instance of abstract "' . $instanceKey . '"');
        }
        $instances = $this->getResolvedSuperTypes($instances);
        $instance = $instances[$instanceKey];

        $serverFactory = new ServerFactory();

        // Create deployment object
        $deployment = new Deployment();
        $deployment->setName($instanceKey);
        foreach ($instance as $key => $value) {
            if ($key === 'servers') {
                $servers = [];
                $serversArray = $this->getResolvedSuperTypes($value);
                foreach ($serversArray as $serverName => $serverData) {
                    $serverData['deployment'] = $deployment;
                    $servers[] = $serverFactory->createServer($serverName, $serverData);
                }
                $value = $servers;
            }

            $setterFunctionName = 'set' . ucfirst($key);
            if (method_exists($deployment, $setterFunctionName)) {
                call_user_func([$deployment, $setterFunctionName], $value);
            } else {
                $deployment->setProperty($key, $value);
            }
        }

        return $deployment;
    }

    protected function getResolvedSuperTypes(array $items): array
    {
        foreach ($items as &$item) {
            while (!empty($item['superTypes'])) {
                $isAbstract = !empty($item['abstract']);
                $superTypes = $item['superTypes'];
                unset($item['superTypes']);
                foreach ($superTypes as $superType) {
                    $item = array_replace_recursive(
                        $items[$superType],
                        $item
                    );
                }
                if (!$isAbstract && array_key_exists('abstract', $item)) {
                    unset($item['abstract']);
                }
            }
        }
        unset($item);
        foreach ($items as $key => $item) {
            if (!empty($item['abstract'])) {
                unset($items[$key]);
            }
        }
        return $items;
    }
}
