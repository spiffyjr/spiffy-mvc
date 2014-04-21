<?php

namespace Spiffy\Mvc;

use Symfony\Component\Console\Application as BaseConsoleApplication;
use Symfony\Component\Console\Command\Command;

class ConsoleApplication extends BaseConsoleApplication
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        parent::__construct($application->getName(), $application->getVersion());

        $this->application = $application;
    }

    /**
     * {@inheritDoc}
     */
    public function add(Command $command)
    {
        if ($command instanceof ConsoleCommand) {
            $command->setInjector($this->getInjector());
        }
        return parent::add($command);
    }

    /**
     * @return \Spiffy\Inject\Injector
     */
    public function getInjector()
    {
        return $this->getApplication()->getInjector();
    }

    /**
     * @return \Spiffy\Mvc\Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}
