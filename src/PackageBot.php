<?php
namespace Extensions\PackageBot;
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

use Extensions\PackageBot\Transporters\CzechPost;
use Nette;

class PackageBot extends Nette\Object
{
    public static $namespace = 'Extensions\PackageBot';

    /** @var array */
    private $transporters;

    /** @var Nette\Caching\Cache */
    private $cache;

    /**
     * PackageBot constructor.
     * @param Nette\Caching\IStorage $cacheStorage
     * @param array $transporters
     */
    public function __construct(Nette\Caching\IStorage $cacheStorage, array $transporters)
    {
        $this->cache = new Nette\Caching\Cache($cacheStorage, self::$namespace);
        $this->transporters = $transporters;
    }
    
    public function test()
    {
        $transporter = new CzechPost($this->transporters['czechPost']);
    }
}