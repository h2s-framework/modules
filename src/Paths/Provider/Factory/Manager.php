<?php

namespace Siarko\Modules\Paths\Provider\Factory;

use Siarko\Api\Factory\AbstractFactory;
use Siarko\Paths\Provider\AbstractPathProvider;
use Siarko\Modules\Config\ModuleConfig;

class Manager
{

    /**
     * @param AbstractFactory $defaultFactory
     * @param AbstractFactory[] $specializedFactories
     */
    public function __construct(
        private readonly AbstractFactory $defaultFactory,
        private readonly array $specializedFactories = []
    )
    {
    }

    /**
     * @param string $type
     * @param string $moduleRootPath
     * @param string $moduleLocalPath
     * @param ModuleConfig $moduleConfig
     * @return AbstractPathProvider
     */
    public function create(string $type, string $moduleRootPath, string $moduleLocalPath, ModuleConfig $moduleConfig): AbstractPathProvider
    {
        if (array_key_exists($type, $this->specializedFactories)) {
            $factory = $this->specializedFactories[$type];
        }
        else{
            $factory = $this->defaultFactory;
        }
        return $factory->create([
            'moduleRootPath' => $moduleRootPath,
            'moduleLocalPath' => $moduleLocalPath,
            'moduleConfig' => $moduleConfig
        ]);

    }

}