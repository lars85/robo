<?php

namespace LarsMalach\Robo\Factory;

use LarsMalach\Robo\Model\Server;

class ServerFactory
{
    public function createServer(string $serverName, array $properties): Server
    {
        $server = new Server();
        $server->setName($serverName);
        foreach ($properties as $key => $value) {
            $setterFunctionName = 'set' . ucfirst($key);
            if (method_exists($server, $setterFunctionName)) {
                call_user_func([$server, $setterFunctionName], $value);
            } else {
                $server->setProperty($key, $value);
            }
        }
        return $server;
    }
}