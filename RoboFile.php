<?php
use Symfony\Component\Finder\Finder;

class RoboFile extends \Robo\Tasks
{
    use \LarsMalach\Robo\Task\Deployment\loadTasks;

    public function deploy($instanceKey, array $opt = ['tag|t' => null, 'branch|b' => null])
    {
        $this->stopOnFail();
        $this->deplInit($instanceKey, 'RoboServers.yaml' /* you can use yaml or json files */, $opt);
        $this->collectionBuilder()
            ->addTask($this->deplGitClone())
            ->addTask($this->deplComposerInstall())
            ->addTask($this->deplBowerInstall())
            ->addTask($this->deplDeployFiles())
            ->addTask($this->deplRemoveLocalTemporaryDirectory())
            ->addTask($this->deplCreateMaintenanceFlag())
            ->addTask($this->deplRelease())
            ->addTask($this->deplMagentoCacheClear())
            ->addTask($this->deplRemoveMaintenanceFlag())
            ->addTask($this->deplRemoveOldReleases())
            ->run();
    }

    public function pharBuild()
    {
        $files = Finder::create()
            ->ignoreVCS(true)
            ->files()
            ->name('*.php')
            ->name('GeneratedWrapper.tmpl')
            ->path('src')
            ->path('vendor')
            ->notPath('docs')
            ->notPath('/vendor\/.*\/tests\//')
            ->in(__DIR__);

        $pharTask = $this->taskPackPhar('robo.phar')
            ->compress()
            ->addFile('robo', 'vendor/consolidation/robo/robo')
            ->executable('vendor/consolidation/robo/robo');
        $pharTask->addFiles($files);

        $chmodTask = $this->taskFilesystemStack()
            ->chmod('robo.phar', 0777);

        return $this->collectionBuilder()
            ->addTask($pharTask)
            ->addTask($chmodTask)
            ->run();
    }

    public function pharInstall()
    {
        return $this->taskExec('cp')
            ->arg('robo.phar')
            ->arg('/usr/local/bin/robo')
            ->run();
    }
}
