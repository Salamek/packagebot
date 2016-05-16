<?php
namespace Extensions\PackageBot;
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

use Extensions\PackageBot\Transporters\CzechPost;
use Extensions\PackageBot\Transporters\ITransporter;
use Extensions\PackageBot\Transporters\PPL;
use Extensions\PackageBot\Transporters\Ulozenka;
use Nette;

class PackageBot extends Nette\Object
{
    public static $namespace = 'Extensions\PackageBot';

    /** @var array */
    private $transporters;

    /** @var array */
    private $sender;

    /** @var string */
    private $cookieJar;

    private $botStorage;

    /** @var Nette\Caching\Cache */
    private $cache;
    
    const TRANSPORTER_CZECH_POST = 'czechPost';
    const TRANSPORTER_PPL = 'ppl';
    const TRANSPORTER_ULOZENKA = 'ulozenka';

    /**
     * PackageBot constructor.
     * @param Nette\Caching\IStorage $cacheStorage
     * @param array $transporters
     * @param array $sender
     * @param IPackageBotStorage $botStorage
     * @param string $cookieJar
     */
    public function __construct(Nette\Caching\IStorage $cacheStorage, array $transporters, array $sender, IPackageBotStorage $botStorage, $cookieJar = 'cookieJar.txt')
    {
        $this->cache = new Nette\Caching\Cache($cacheStorage, self::$namespace);
        $this->transporters = $transporters;
        $this->sender = $sender;
        $this->cookieJar = $cookieJar;
        $this->botStorage = $botStorage;
    }

    /**
     *
     */
    public function flush()
    {
        foreach($this->transporters AS $transporter => $config)
        {
            if ($config['enabled'])
            {
                $className = ucfirst($transporter);
                /** @var ITransporter $iTransporter */
                $iTransporter = new $className($config, $this->sender, $this->botStorage, $this->cookieJar);
                $iTransporter->doFlush();
            }
        }
    }

    /**
     * @param PackageBotPackage $package
     * @param PackageBotReceiver $receiver
     * @param string $transporter
     * @return PackageBotParcelInfo
     * @throws \Exception
     */
    public function parcel(PackageBotPackage $package, PackageBotReceiver $receiver, $transporter = self::TRANSPORTER_CZECH_POST)
    {
        if (!array_key_exists($transporter, $this->transporters))
        {
            throw new \Exception(sprintf('Transporter %s is not configured', $transporter));
        }

        if (!$this->transporters[$transporter]['enabled'])
        {
            throw new \Exception(sprintf('Transporter %s is not enabled', $transporter));
        }


        switch($transporter)
        {
            case self::TRANSPORTER_CZECH_POST:
            case self::TRANSPORTER_PPL:
            case self::TRANSPORTER_ULOZENKA:
                $className = ucfirst($transporter);
                /** @var ITransporter $iTransporter */
                $iTransporter = new $className($this->transporters[$transporter], $this->sender, $this->botStorage, $this->cookieJar);
                break;

            default:
                //Allow custom transporters here
                throw new \Exception('Unknow transporter');
                break;
        }

        return $iTransporter->doParcel($package, $receiver);
    }
}