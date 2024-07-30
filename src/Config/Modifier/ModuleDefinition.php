<?php

namespace Siarko\Modules\Config\Modifier;

use Siarko\ConfigFiles\Api\Modifier\ModifierInterface;
use Siarko\ConfigFiles\Api\Modifier\ModifierManagerInterface;
use Siarko\DependencyManager\Config\DMKeys;
use Siarko\DependencyManager\Config\Init\Modifier\Builder\ArgumentInjector;
use Siarko\DependencyManager\Config\Init\Modifier\Builder\TypeInjector;
use Siarko\DependencyManager\Config\Init\Modifier\DirnameInjector;
use Siarko\DependencyManager\Type\TypedType;
use Siarko\Files\Api\FileInterface;
use Siarko\Modules\Config\ModuleConfig;
use Siarko\Modules\Exception\ModuleConfigException;
use Siarko\Modules\ModuleLibrary;

class ModuleDefinition implements ModifierInterface
{

    private const MODULE_TYPE_PREFIX = 'MODULE_CONFIG_DEFINITION_';

    public const MODULE_DEFINITION_KEY = 'module';
    public const MODULE_DEFINITION_ID = 'id';
    public const MODULE_DEFINITION_NAME = 'name';
    public const MODULE_DEFINITION_VERSION = 'version';
    public const MODULE_DEFINITION_DESCRIPTION = 'description';
    public const MODULE_DEFINITION_CONFIG_PATH = 'configPath';
    private const ARGUMENTS_KEY = DMKeys::ARGUMENTS.'/'.ModuleLibrary::class.'/config/';

    /**
     * @param TypeInjector $typeInjector
     * @param ArgumentInjector $argumentInjector
     */
    public function __construct(
        private readonly TypeInjector $typeInjector,
        private readonly ArgumentInjector $argumentInjector
    )
    {
    }

    /**
     * @param ModifierManagerInterface $manager
     * @param FileInterface $file
     * @param array $config
     * @return array
     * @throws \Exception
     */
    public function apply(ModifierManagerInterface $manager, FileInterface $file, array $config): array
    {
        $moduleDefinition = $config[self::MODULE_DEFINITION_KEY] ?? null;
        if($moduleDefinition !== null){
            $this->verifyConfigScope($file);
            $moduleData = $this->parseModuleData($moduleDefinition, $file);
            $moduleData[self::MODULE_DEFINITION_CONFIG_PATH] = DirnameInjector::VAR_NAME;

            $moduleId = $moduleData[self::MODULE_DEFINITION_ID];
            $configTypeName = self::MODULE_TYPE_PREFIX.mb_strtoupper($moduleId);

            $config = $this->injectModuleConfig($config, $moduleData, $configTypeName);
            unset($config[self::MODULE_DEFINITION_KEY]);
        }
        return $config;
    }

    /**
     * @param array $config
     * @param array $moduleData
     * @param string $typeName
     * @return array
     */
    private function injectModuleConfig(array $config, array $moduleData, string $typeName): array
    {
        $config = $this->typeInjector->injectType(
            $config, $typeName,
            ModuleConfig::class, TypedType::TYPE_NEW
        );
        $config = $this->argumentInjector->injectArgumentArray(
            $config, $typeName, $moduleData
        );
        $moduleId = $moduleData[self::MODULE_DEFINITION_ID];
        return $this->argumentInjector->injectArgument(
            $config, ModuleLibrary::class, 'config', [$moduleId => $typeName]
        );
    }

    /**
     * @return array
     */
    public function getDependencyOrder(): array
    {
        return [
            ModifierInterface::DEPENDENCY_AFTER => [
                DirnameInjector::class
            ]
        ];
    }

    /**
     * @param mixed $moduleDefinition
     * @param FileInterface $file
     * @return array
     * @throws ModuleConfigException
     */
    private function parseModuleData(mixed $moduleDefinition, FileInterface $file): array
    {
        if(is_array($moduleDefinition)) {
            if(!($moduleDefinition[self::MODULE_DEFINITION_ID] ?? null)){
                throw new ModuleConfigException('Invalid module definition - missing ID', $file->getPath());
            }
            if(!($moduleDefinition[self::MODULE_DEFINITION_NAME] ?? null)){
                throw new ModuleConfigException('Invalid module definition - missing NAME', $file->getPath());
            }
            $result = $moduleDefinition;
        }elseif(is_string($moduleDefinition)){
            $result = [
                self::MODULE_DEFINITION_ID => $moduleDefinition,
                self::MODULE_DEFINITION_NAME => $moduleDefinition
            ];
        }else{
            throw new ModuleConfigException('Invalid module definition', $file->getPath());
        }

        return $result;
    }

    /**
     * @param FileInterface $file
     * @return void
     * @throws ModuleConfigException
     */
    private function verifyConfigScope(FileInterface $file): void
    {
        $parentDir = basename($file->getPathInfo()->getDirname())[0] ?? '';
        if(ctype_lower($parentDir)){ //scoped configs cant declare modules
            throw new ModuleConfigException('Cannot declare module in scoped config', $file->getPath());
        }
    }
}