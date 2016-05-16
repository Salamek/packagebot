<?php

namespace Salamek\PackageBot\DI;

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
        'transporters' => [],
        'sender' => []
    ];


    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('packageBot'))
            ->setClass('Salamek\PackageBot\PackageBot', ['@cacheStorage', $config['transporters'], $config['sender']]); //, '@' . $config['target']
    }

    /**
     * @param ClassType $class
     */
    public function afterCompile(ClassType $class)
    {

    }

}