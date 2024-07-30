<?php

namespace Siarko\Modules\Config;

use Siarko\Serialization\Api\Attribute\Serializable;

class ModuleConfig
{

    /**
     * @param string $id
     * @param string $name
     * @param string $configPath
     * @param string|null $version
     * @param string|null $description
     */
    public function __construct(
        #[Serializable] private readonly string  $id,
        #[Serializable] private readonly string  $name,
        #[Serializable] private readonly string  $configPath,
        #[Serializable] private readonly ?string $version = null,
        #[Serializable] private readonly ?string $description = null
    )
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    /**
     * @return string
     */
    public function getRootPath(): string
    {
        return dirname($this->configPath);
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}