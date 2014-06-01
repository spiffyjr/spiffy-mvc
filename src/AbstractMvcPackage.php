<?php

namespace Spiffy\Mvc;

use Spiffy\Package\Feature\OptionsProviderTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\Finder;

abstract class AbstractMvcPackage implements MvcPackage
{
    use OptionsProviderTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function initializeConsole(ConsoleApplication $console)
    {
        $consoleDir = realpath($this->getPath() . '/Console');
        if (!$consoleDir) {
            return;
        }

        $finder = new Finder();
        $finder
            ->files()
            ->ignoreUnreadableDirs()
            ->name('*Command.php')
            ->in($consoleDir);

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

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        if ($this->path) {
            return $this->path;
        }

        $refl = new \ReflectionObject($this);
        $this->path = dirname($refl->getFileName());
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        $config = $this->getPath() . '/../config/config.php';
        return file_exists($config) ? include $config : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getControllers()
    {
        $controllers = $this->getPath() . '/../config/controllers.php';
        return file_exists($controllers) ? include $controllers : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutes()
    {
        $routes = $this->getPath() . '/../config/routes.php';
        return file_exists($routes) ? include $routes : [];
    }

    /**
     * {@inheritDoc}
     */
    public function getServices()
    {
        $services = $this->getPath() . '/../config/services.php';
        return file_exists($services) ? include $services : [];
    }

    /**
     * {@inheritDoc}
     */
    final public function getName()
    {
        if ($this->name) {
            return $this->name;
        }

        $name = preg_replace('@Package$@', '', $this->getNamespace());
        $name = str_replace('\\', '.', $name);
        $name = strtolower($name);

        if (strstr($name, '.')) {
            $this->name = substr($name, strpos($name, '.') + 1);
        } else {
            $this->name = $name;
        }

        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    final public function getNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strrpos($class, '\\'));
    }
}
