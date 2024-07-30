<?php

namespace Siarko\Modules\Paths\Provider;

use Siarko\Paths\Provider\AbsolutePathProvider;

class DefaultModulePathProvider extends AbsolutePathProvider
{
    /**
     * @param string $moduleLocalPath
     * @param string $moduleRootPath
     */
    public function __construct(string $moduleLocalPath, string $moduleRootPath)
    {
        parent::__construct($moduleRootPath.DIRECTORY_SEPARATOR.$moduleLocalPath);
    }


}