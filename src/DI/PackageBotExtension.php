<?php

namespace Salamek\PackageBot\DI;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Kdyby\Console\DI\ConsoleExtension;

/**
 * Description of PackageBotExtension
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
final class PackageBotExtension extends CompilerExtension
{

    /** @var array */
    private $defaults = [
        'packageStorage' => null,
        'seriesNumberStorage' => null,
        'transporterDataGroupStorage' => null,
        'transporterDataItemStorage' => null,
        'temp' => null,
        'transporters' => [],
        'sender' => []
    ];


    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('packageBot'))
            ->setClass('Salamek\PackageBot\PackageBot', [
                '@cacheStorage',
                $config['transporters'],
                $config['sender'],
                '@' . $config['packageStorage'],
                '@' . $config['seriesNumberStorage'],
                '@' . $config['transporterDataGroupStorage'],
                '@' . $config['transporterDataItemStorage'],
                $config['temp']
            ]);

        $this->loadConsole();
    }

    /**
     * @param ClassType $class
     */
    public function afterCompile(ClassType $class)
    {

    }

    protected function loadConsole()
    {
        $builder = $this->getContainerBuilder();
        foreach ($this->loadFromFile(__DIR__ . '/console.neon') as $i => $command) {
            $cli = $builder->addDefinition($this->prefix('cli.' . $i))
                ->addTag(ConsoleExtension::TAG_COMMAND)
                ->setInject(FALSE); // lazy injects
            if (is_string($command)) {
                $cli->setClass($command);
            } else {
                throw new \InvalidArgumentException;
            }
        }
    }

}