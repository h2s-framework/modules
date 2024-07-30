<?php

namespace Siarko\Modules\Paths\Provider;

use Siarko\Paths\Provider\AbsolutePathProvider;
use Siarko\Modules\Config\ModuleConfig;

class ModuleRelativePathProvider extends AbsolutePathProvider
{
    /**
     * @param string $moduleLocalPath
     * @param string $moduleRootPath
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        string $moduleLocalPath,
        string $moduleRootPath,
        private readonly ModuleConfig $moduleConfig,
    )
    {
        parent::__construct($moduleRootPath.DIRECTORY_SEPARATOR.$moduleLocalPath);
    }


    /**
     * Handles paths that are relative to the module root (ModuleID::path/to/file)
     * @param $id
     * @return string
     * @throws \Exception
     */
    public function getConstructedPath($id = null): string
    {
        $pathParts = explode("::", $id ?? '');
        if(count($pathParts) != 2 || $pathParts[0] !== $this->moduleConfig->getName()) {
            return '';
        }
        return parent::getConstructedPath($pathParts[1]);
    }


}