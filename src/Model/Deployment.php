<?php

namespace LarsMalach\Robo\Model;

class Deployment
{
    /** @var array|Server[] */
    protected $servers = [];

    /** @var string */
    protected $tag = '';

    /** @var string */
    protected $branch = 'master';

    /** @var string */
    protected $name;

    /** @var string */
    protected $projectName;

    /** @var \DateTime */
    protected $createdAt;

    /** @var string */
    protected $repository;

    /** @var int */
    protected $keepReleases = 5;

    /** @var string */
    protected $webDirectory = '';

    /** @var array */
    protected $properties = [];

    const CONTEXT_PRODUCTION = 'production';
    const CONTEXT_DEVELOPMENT = 'development';

    /** @var string */
    protected $context = self::CONTEXT_PRODUCTION;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getReleaseName(): string
    {
        return $this->createdAt->format('Y-m-d_H-i-s') . '_' . $this->getRevisionName();
    }

    public function getLocalTemporaryPath(): string
    {
        return '/tmp/robo/' . $this->getProjectName(). '/' . $this->getName() . '/' . $this->getReleaseName();
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getProjectName(): string
    {
        $projectName = $this->projectName;
        if (empty($projectName)) {
            $projectName = parse_url($this->getRepository(), PHP_URL_PATH);
            $projectName = strtok($projectName, '.');
            $projectName = str_replace('/', '_', $projectName);
        }
        return $projectName;
    }

    public function setProjectName(string $projectName): self
    {
        $this->projectName = $projectName;
        return $this;
    }

    /**
     * @return Server[]
     */
    public function getServers(): array
    {
        return $this->servers;
    }

    public function setServers(array $servers): self
    {
        $this->servers = [];
        foreach ($servers as $server) {
            $this->setServer($server);
        }
        return $this;
    }

    public function setServer(Server $server): self
    {
        $this->servers[] = $server;
        return $this;
    }

    public function getTag(): string
    {
        return $this->tag;
    }


    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function setBranch(string $branch): self
    {
        $this->branch = $branch;
        return $this;
    }

    /**
     * Returns the tag name if its not empty, otherwise the branch
     */
    public function getRevisionName(): string
    {
        return $this->getTag() ?: $this->getBranch();
    }

    /**
     * 'Production' or 'Developement'.
     * @see CONTEXT_*
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * 'Production' or 'Developement'.
     * @see CONTEXT_*
     */
    public function setContext(string $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function setRepository(string $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    public function getKeepReleases(): int
    {
        return $this->keepReleases;
    }

    public function setKeepReleases(int $keepReleases): self
    {
        $this->keepReleases = $keepReleases;
        return $this;
    }

    /**
     * e.g. 'Web' or 'src'
     */
    public function getWebDirectory(): string
    {
        return trim($this->webDirectory, '/');
    }

    /**
     * e.g. 'Web' or 'src'
     */
    public function setWebDirectory(string $webDirectory): self
    {
        $this->webDirectory = $webDirectory;
        return $this;
    }

    public function getProperty(string $key)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }

    public function setProperty(string $key, $value)
    {
        $this->properties[$key] = $value;
    }
}
