<?php

namespace Siarko\Modules;

use Siarko\Modules\Config\ModuleConfig;

class ModuleLibrary
{

    /**
     * @param ModuleConfig[] $config
     */
    public function __construct(
        private array $config = []
    )
    {
    }

    /**
     * @param string $id
     * @return ModuleConfig|null
     */
    public function getModule(string $id): ?ModuleConfig
    {
        return $this->config[$id] ?? null;
    }

    /**
     * @return ModuleConfig[]
     */
    public function getAllModules(): array
    {
        return $this->config;
    }
}