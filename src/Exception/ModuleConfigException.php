<?php

namespace Siarko\Modules\Exception;

class ModuleConfigException extends \Exception
{

    public function __construct(string $message, protected string $file, protected int $line = 0)
    {
        parent::__construct($message);
    }

}