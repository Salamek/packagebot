<?php

namespace Extensions\PackageBot\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;

/**
 * Description of PackageBotExtension
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
final class PackageBotExtension extends CompilerExtension
{

    /** @var array */
    private $defaults = [
        'transporters' => []
    ];


    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('importer'))
            ->setClass('Extensions\PackageBot\PackageBot', ['@cacheStorage', $config['transporters']]); //, '@' . $config['target']
    }

    /**
     * @param ClassType $class
     */
    public function afterCompile(ClassType $class)
    {

    }

}