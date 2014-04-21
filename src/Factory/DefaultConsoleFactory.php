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
        $finder = $this->createFinder($pm->getPackages());

        try {
            $finder->getIterator();
        } catch (\LogicException $ex) {
            return;
        }

        $this->addCommands($finder, $console);
    }

    /**
     * @param \ArrayObject $packages
     * @return Finder
     */
    protected function createFinder(\ArrayObject $packages)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*Command.php');

        foreach ($packages as $package) {
            $refl = new \ReflectionClass($package);
            $dir = realpath(dirname($refl->getFileName()) . '/Command');

            if (!is_dir($dir)) {
                continue;
            }

            $finder->in($dir);
        }

        return $finder;
    }

    /**
     * @param Finder $finder
     * @param ConsoleApplication $console
     * @codeCoverageIgnore Ignored because get_declared_classes() is hard to test. If you have any idea I'm open!
     */
    protected function addCommands(Finder $finder, ConsoleApplication $console)
    {
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
