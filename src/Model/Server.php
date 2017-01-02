<?php

namespace LarsMalach\Robo\Model;

use LarsMalach\Robo\Helper\TemplateHelper;

class Server
{
    use Traits\Properties;

    /** @var string */
    protected $name;

    /** @var string */
    protected $host;

    /** @var string */
    protected $user = '';

    /** @var string */
    protected $path;

    /** @var Deployment */
    protected $deployment;

    public function getName(): string
    {
        return !empty($this->name) ? TemplateHelper::renderString($this->name, ['server' => $this]) : $this->getHost();
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getHost(): string
    {
        return TemplateHelper::renderString($this->host, ['server' => $this]);
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getUser(): string
    {
        return TemplateHelper::renderString($this->user, ['server' => $this]);
    }

    public function setUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPath(string $type = 'rootPath'): string
    {
        switch ($type) {
            case Deployment::PATH_RELEASE:
                $path = $this->getReleasePath();
                break;
            case Deployment::PATH_WEB:
                $path = $this->getWebPath();
                break;
            case Deployment::PATH_ROOT:
            default:
                $path = rtrim(TemplateHelper::renderString($this->path, ['server' => $this]), '/');
        }
        return $path;
    }

    public function getReleasePath(): string
    {
        return rtrim($this->getPath() . '/' . $this->getDeployment()->getReleaseName(), '/');
    }

    public function getWebPath(): string
    {
        return rtrim($this->getReleasePath() . '/' . $this->getDeployment()->getWebDirectory(), '/');
    }

    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getDeployment(): Deployment
    {
        return $this->deployment;
    }

    public function setDeployment(Deployment $deployment): self
    {
        $this->deployment = $deployment;
        return $this;
    }
}
