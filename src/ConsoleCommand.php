<?php

namespace Spiffy\Mvc;

use Spiffy\Inject\Injector;
use Symfony\Component\Console\Command\Command;

class ConsoleCommand extends Command
{
    /**
     * @var Injector
     */
    protected $injector;

    /**
     * @param Injector $i
     */
    public function setInjector(Injector $i)
    {
        $this->injector = $i;
    }

    /**
     * @return Injector
     */
    public function getInjector()
    {
        return $this->injector;
    }
}
