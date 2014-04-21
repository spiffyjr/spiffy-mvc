<?php

namespace Spiffy\Mvc\Factory;

use Spiffy\Mvc\ConsoleApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

class DefaultConsoleFactory
{
    public function create(array $config)
    {
        $appFactory = new DefaultApplicationFactory();
        $console = new ConsoleApplication($appFactory->create($config));

        $this->injectCommands($console);

        return $console;
    }

    /**
     * @param ConsoleApplication $console
     */
    protected function injectCommands(ConsoleApplication $console)
    {
        $app = $console->getApplication();

        /** @var \Spiffy\Package\PackageManager $pm */
        $pm = $app->getInjector()->get('package-manager');

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*Command.php');

        foreach ($pm->getPackages() as $name => $package) {
            $refl = new \ReflectionClass($package);
            $dir = realpath(dirname($refl->getFileName()) . '/Command');

            if (!is_dir($dir)) {
                continue;
            }

            $finder->in($dir);
        }

        try {
            $finder->getIterator();
        } catch (\LogicException $ex){
            return;
        }

        foreach ($finder as $file) {
            $classes = get_declared_classes();
            include_once $file;
            $newClasses = get_declared_classes();

            foreach (array_diff($newClasses, $classes) as $className) {
                if ($className == 'Spiffy\Mvc\ConsoleCommand') {
                    continue;
                }

                $refl = new \ReflectionClass($className);
                if ($refl->isAbstract()) {
                    continue;
                }
                $command = $refl->newInstance();
                if (!$command instanceof Command) {
                    continue;
                }
                $console->add(new $command);
            }
        }
    }
}
