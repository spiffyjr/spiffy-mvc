<?php

namespace Spiffy\Mvc\Exception;

class MissingMvcEventException extends \RuntimeException
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct('Failed to dispatch: missing action but no MvcEvent available.');
    }
}
