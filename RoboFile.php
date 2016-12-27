<?php

use Symfony\Component\Finder\Finder;

class RoboFile extends \Robo\Tasks
{
    /**
     * Build the Robo phar executable.
     */
    public function pharBuild()
    {
        // Decide which files we're going to pack
        $files = Finder::create()->ignoreVCS(true)
            ->files()
            ->name('*.php')
            ->name('*.exe') // for 1symfony/console/Resources/bin/hiddeninput.exe
            ->name('GeneratedWrapper.tmpl')
            ->path('src')
            ->path('vendor')
            ->notPath('docs')
            ->notPath('/vendor\/.*\/[Tt]est/')
            ->in(__DIR__);

        // Build the phar
        // Create a collection builder to hold the temporary
        // directory until the pack phar task runs.
        $collection = $this->collectionBuilder();
        return $collection
            ->taskPackPhar('robo.phar')
            ->addFiles($files)
            ->addFile('robo', 'robo')
            ->executable('robo')
            ->taskFilesystemStack()
            ->chmod('robo.phar', 0777)
            ->run();
    }

    /**
     * Install Robo phar.
     *
     * Installs the Robo phar executable in /usr/bin. Uses 'sudo'.
     */
    public function pharInstall()
    {
        return $this->taskExec('sudo cp')
            ->arg('robo.phar')
            ->arg('/usr/bin/robo')
            ->run();
    }

    /**
     * Publish Robo phar.
     *
     * Commits the phar executable to Robo's GitHub pages site.
     */
    public function pharPublish()
    {
        $this->pharBuild();

        $this->collectionBuilder()
            ->taskFilesystemStack()
            ->rename('robo.phar', 'robo-release.phar')
            ->taskGitStack()
            ->checkout('site')
            ->pull('origin site')
            ->taskFilesystemStack()
            ->remove('robotheme/robo.phar')
            ->rename('robo-release.phar', 'robotheme/robo.phar')
            ->taskGitStack()
            ->add('robotheme/robo.phar')
            ->commit('Update robo.phar to ' . \Robo\Robo::VERSION)
            ->push('origin site')
            ->checkout('master')
            ->run();
    }
}
