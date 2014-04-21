<?php

namespace Spiffy\Mvc\TestAsset\Application\Command;

use Spiffy\Mvc\ConsoleCommand;

class TestCommand extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName('TestCommand');
    }
}
