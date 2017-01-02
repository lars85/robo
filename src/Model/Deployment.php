<?php

namespace LarsMalach\Robo\Model;

use LarsMalach\Robo\Helper\TemplateHelper;

class Deployment
{
    use Traits\Properties;

    /** @var array|Server[] */
    protected $servers = [];

    /** @var string */
    protected $tag = '';

    /** @var string */
    protected $branch = 'master';

    /** @var string */
    protected $name;

    /** @var string */
    protected $releaseName = 'release_{{ deployment.createdAt | date("Y-m-d_H-i-s") }}_{{ deployment.revisionName }}';

    /** @var string */
    protected $localPath = '/tmp/robo/{{ deployment.projectName }}/{{ deployment.name }}';

    /** @var string */
    protected $projectName;

    /** @var \DateTime */
    protected $createdAt;

    /** @var string */
    protected $repository = '{{ repositoryPath }}';

    /** @var int */
    protected $keepReleases = 5;

    /** @var string */
    protected $webDirectory = '';

    /** @var string */
    protected $maintenanceFlagFileName = 'maintenance.flag';

    const CONTEXT_PRODUCTION = 'production';
    const CONTEXT_DEVELOPMENT = 'development';

    const PATH_WEB = 'webPath';
    const PATH_RELEASE = 'releasePath';
    const PATH_ROOT = 'rootPath';

    /** @var string */
    protected $context = self::CONTEXT_PRODUCTION;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getReleaseName(): string
    {
        return TemplateHelper::renderString($this->releaseName, ['deployment' => $this]);
    }

    public function setReleaseName(string $releaseName): self
    {
        $this->releaseName = $releaseName;
        return $this;
    }

    public function getName(): string
    {
        return TemplateHelper::renderString($this->name, ['deployment' => $this]);
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getProjectName(): string
    {
        $repositoryPath = parse_url($this->getRepository(), PHP_URL_PATH);
        $repositoryPath = strtok($repositoryPath, '.');
        $repositoryPath = str_replace('/', '_', $repositoryPath);

        return TemplateHelper::renderString(
            $this->projectName,
            ['deployment' => $this, 'repositoryPath' => $repositoryPath]
        );
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
        $this->servers = $servers;
        return $this;
    }

    public function addServer(Server $server): self
    {
        $this->servers[] = $server;
        return $this;
    }

    public function getTag(): string
    {
        return TemplateHelper::renderString($this->tag, ['deployment' => $this]);
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getBranch(): string
    {
        return TemplateHelper::renderString($this->branch, ['deployment' => $this]);
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
        return TemplateHelper::renderString($this->context, ['deployment' => $this]);
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

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getRepository(): string
    {
        return TemplateHelper::renderString($this->repository, ['deployment' => $this]);
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
        return trim(TemplateHelper::renderString($this->webDirectory, ['deployment' => $this]), '/');
    }

    /**
     * e.g. 'Web' or 'src'
     */
    public function setWebDirectory(string $webDirectory): self
    {
        $this->webDirectory = $webDirectory;
        return $this;
    }

    public function getLocalPath(string $type = 'localRootPath'): string
    {
        switch ($type) {
            case self::PATH_WEB:
                $path = $this->getWebPath();
                break;
            case self::PATH_RELEASE:
                $path = $this->getReleasePath();
                break;
            case self::PATH_ROOT:
            default:
                $path = rtrim(TemplateHelper::renderString($this->localPath, ['deployment' => $this]), '/');
        }
        return $path;
    }

    public function setLocalPath(string $localPath): self
    {
        $this->localPath = $localPath;
        return $this;
    }

    public function getReleasePath(): string
    {
        return rtrim($this->getLocalPath() . '/' . $this->getReleaseName(), '/');
    }

    public function getWebPath(): string
    {
        return rtrim($this->getReleasePath() . '/' . $this->getWebDirectory(), '/');
    }

    public function getMaintenanceFlagFileName(): string
    {
        return TemplateHelper::renderString($this->maintenanceFlagFileName, ['deployment' => $this]);
    }

    public function setMaintenanceFlagFileName(string $maintenanceFlagFileName)
    {
        $this->maintenanceFlagFileName = $maintenanceFlagFileName;
    }
}
