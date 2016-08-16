<?php
namespace Salamek\PackageBot;
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

use Salamek\PackageBot\Transporters\CzechPost;
use Salamek\PackageBot\Transporters\ITransporter;
use Salamek\PackageBot\Transporters\Ppl;
use Salamek\PackageBot\Transporters\Ulozenka;
use Nette;

class PackageBot extends Nette\Object
{
    public static $namespace = 'Salamek\PackageBot';

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
    const TRANSPORTER_PPL = 'professionalParcelLogistic';
    const TRANSPORTER_ULOZENKA = 'ulozenka';

    const PACKAGE_LABEL_FULL = 1;
    const PACKAGE_LABEL_QUARTER = 2;

    /**
     * PackageBot constructor.
     * @param Nette\Caching\IStorage $cacheStorage
     * @param array $transporters
     * @param array $sender
     * @param IPackageBotStorage $botStorage
     * @param string $tempDir
     */
    public function __construct(Nette\Caching\IStorage $cacheStorage, array $transporters, array $sender, IPackageBotStorage $botStorage, $tempDir = null)
    {
        $this->cache = new Nette\Caching\Cache($cacheStorage, self::$namespace);
        $this->transporters = $transporters;
        $this->sender = $sender;

        if (is_null($tempDir))
        {
            $cookieJar = tempnam(sys_get_temp_dir(), 'cookieJar.txt');
        }
        else
        {
            $cookieJar = $tempDir.'/cookieJar.txt';
        }

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
                $className = 'Salamek\\PackageBot\\Transporters\\'.ucfirst($transporter);
                /** @var ITransporter $iTransporter */
                $iTransporter = new $className($config, $this->sender, $this->botStorage, $this->cookieJar);
                $iTransporter->doFlush();
            }
        }
    }

    /**
     * @param PackageBotPackage $package
     * @param PackageBotReceiver $receiver
     * @param PackageBotPaymentInfo $paymentInfo
     * @param string $transporter
     * @return string
     * @throws \Exception
     */
    public function parcel(PackageBotPackage $package, PackageBotReceiver $receiver, PackageBotPaymentInfo $paymentInfo = null, $transporter = self::TRANSPORTER_CZECH_POST)
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
                $className = 'Salamek\\PackageBot\\Transporters\\'.ucfirst($transporter);
                /** @var ITransporter $iTransporter */
                $iTransporter = new $className($this->transporters[$transporter], $this->sender, $this->botStorage, $this->cookieJar);
                break;

            default:
                //@TODO Allow custom transporters here
                throw new \Exception('Unknow transporter');
                break;
        }

        return $iTransporter->doParcel($package, $receiver, $paymentInfo);
    }

    /**
     * @param $packageId
     * @param string $transporter
     * @param int $decomposition
     * @return mixed
     * @throws \Exception
     */
    public function getPackageLabel($packageId, $transporter = self::TRANSPORTER_CZECH_POST, $decomposition = self::PACKAGE_LABEL_QUARTER)
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
                $className = 'Salamek\\PackageBot\\Transporters\\'.ucfirst($transporter);
                /** @var ITransporter $iTransporter */
                $iTransporter = new $className($this->transporters[$transporter], $this->sender, $this->botStorage, $this->cookieJar);
                break;

            default:
                //@TODO Allow custom transporters here
                throw new \Exception('Unknow transporter');
                break;
        }

        return $iTransporter->doGenerateLabel($packageId, $decomposition);
    }
}