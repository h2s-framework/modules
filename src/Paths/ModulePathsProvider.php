<?php

namespace Siarko\Modules\Paths;

use Siarko\Paths\Provider\AbstractPathProvider;
use Siarko\Paths\Api\Provider\Pool\ProviderBuilderInterface;
use Siarko\Modules\ModuleLibrary;
use Siarko\Modules\Paths\Provider\Factory\Manager;

class ModulePathsProvider implements ProviderBuilderInterface
{

    /**
     * @param ModuleLibrary $moduleLibrary
     * @param Manager $providerFactoryManager
     * @param string[] $pathTypes
     */
    public function __construct(
        private readonly ModuleLibrary $moduleLibrary,
        private readonly Manager       $providerFactoryManager,
        private readonly array         $pathTypes = []
    )
    {
    }

    /**
     * @param string $type
     * @return AbstractPathProvider[]
     */
    public function build(string $type): array
    {
        if (array_key_exists($type, $this->pathTypes)) {
            return $this->getProviders($type);
        }
        return [];
    }

    /**
     * @param string $type
     * @return AbstractPathProvider[]
     */
    private function getProviders(string $type): array
    {
        $moduleLocalPath = $this->pathTypes[$type];
        $result = [];
        $modules = $this->moduleLibrary->getAllModules();
        foreach ($modules as $module) {
            $result[] = $this->providerFactoryManager->create($type, $module->getRootPath(), $moduleLocalPath, $module);
        }
        return $result;
    }

}