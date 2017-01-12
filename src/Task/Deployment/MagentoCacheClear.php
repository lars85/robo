<?php

namespace LarsMalach\Robo\Task\Deployment;

use LarsMalach\Robo\Model\Deployment;
use LarsMalach\Robo\Model\Server;
use Robo\Result;
use Robo\Task\Base\Exec;

class MagentoCacheClear extends BaseTask
{
    public function run()
    {
        $cleanDirTask = (new Exec('rm -rf'))->arg('var/cache/*');
        $cleanCacheTask = new Exec('n98-magerun cache:clean');
        $setupRunTask = new Exec('n98-magerun sys:setup:run');

        // Local
        if ($this->isExecLocal()) {
            $this->runTaskLocal($cleanDirTask, Deployment::PATH_WEB);
            $this->runTaskLocal($cleanCacheTask->arg($this->getCaches()), Deployment::PATH_WEB);
            return $this->runTaskLocal($setupRunTask, Deployment::PATH_WEB);
        }

        // On Servers
        $i = 0;
        foreach ($this->getDeployment()->getServers() as $server) {
            $i++;
            $this->runTaskOnServer($server, $cleanDirTask, Deployment::PATH_WEB);
            if ($i === 1) {
                $this->runTaskOnServer($server, $cleanCacheTask->args($this->getCaches($server)), Deployment::PATH_WEB);
                $this->runTaskOnServer($server, $setupRunTask, Deployment::PATH_WEB);
            }
        }

        return Result::success($this);
    }

    public function getCaches(Server $server = null): array
    {
        // get all cache items
        $cacheListTask = (new Exec('n98-magerun cache:list'))
            ->option('format', 'json');
        if (!empty($server)) {
            $result = $this->runTaskOnServer($server, $cacheListTask, Deployment::PATH_WEB);
        } else {
            $result = $this->runTaskLocal($cacheListTask, Deployment::PATH_WEB);
        }
        $cacheItems = json_decode($result->getOutputData(), true);

        // get excludes
        $excludes = $server->getDeployment()->getProperty('magentoCacheClear.cacheExcludes') ?: [];
        if (!empty($server)) {
            $excludes = array_merge($excludes, $server->getProperty('magentoCacheClear.cacheExcludes') ?: []);
        }

        // remove excluded items
        $caches = [];
        foreach ($cacheItems as $cacheItem) {
            if ($cacheItem['status'] !== 'enabled') {
                continue;
            }
            foreach ($excludes as $exclude) {
                if ($exclude[0] !== '/') {
                    $exclude = preg_quote($exclude, '/');
                    $exclude = '/^' . str_replace(preg_quote('*'), '.*', $exclude) . '$/';
                }
                if (preg_match($exclude, $cacheItem['code'])) {
                    continue 2;
                }
            }
            $caches[] = $cacheItem['code'];
        }
        return $caches;
    }
}