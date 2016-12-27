<?php

namespace LarsMalach\Robo\Model;

class Server
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $host;

    /** @var string */
    protected $user = '';

    /** @var string */
    protected $path;

    /** @var array */
    protected $properties;

    public function getName(): string
    {
        return !empty($this->name) ? $this->name : $this->getHost();
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getReleasePath(Deployment $deployment): string
    {
        return $this->getPath() . '/release_' . $deployment->getReleaseName() . '/';
    }

    public function setPath(string $path): self
    {
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        $this->path = $path;
        return $this;
    }

    public function getProperty(string $key)
    {
        return $this->properties[$key];
    }

    public function setProperty(string $key, $value)
    {
        $this->properties[$key] = $value;
    }
}
