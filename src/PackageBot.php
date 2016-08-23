<?php
namespace Salamek\PackageBot;
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

use Salamek\PackageBot\Enum\Attribute\LabelAttr;
use Salamek\PackageBot\Enum\LabelDecomposition;
use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\Enum\Transporter;
use Salamek\PackageBot\Enum\TransportService;
use Salamek\PackageBot\Exception\WrongDeliveryDataException;
use Salamek\PackageBot\Model\Package;
use Salamek\PackageBot\Transporters\ITransporter;
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
     * @param $transporter
     * @return ITransporter
     * @throws \Exception
     */
    private function getTransporter($transporter)
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
            case Transporter::CZECH_POST:
            case Transporter::PPL:
            case Transporter::ULOZENKA:
                $className = 'Salamek\\PackageBot\\Transporters\\'.ucfirst($transporter);
                /** @var ITransporter $iTransporter */
                $iTransporter = new $className($this->transporters[$transporter], $this->sender, $this->botStorage, $this->cookieJar);
                break;

            default:
                //@TODO Allow custom transporters here ?
                throw new \Exception('Unknow transporter');
                break;
        }

        return $iTransporter;
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
                $iTransporter = $this->getTransporter($transporter);
                $unsentPackages = $this->botStorage->getUnSentPackages($transporter);
                $iTransporter->doSendPackages($unsentPackages);
                $this->botStorage->setSendPackages($transporter, $unsentPackages, new \DateTime());
            }
        }
    }

    /**
     * @param $transportService
     * @return mixed
     * @throws \Exception
     */
    public function transportServiceToTransporter($transportService)
    {
        $map = [
            TransportService::CZECH_POST_PACKAGE_TO_HAND => Transporter::CZECH_POST,
            TransportService::CZECH_POST_PACKAGE_TO_THE_POST_OFFICE => Transporter::CZECH_POST,
            TransportService::PPL_PARCEL_CZ_PRIVATE => Transporter::PPL
        ];

        if (!array_key_exists($transportService, $map))
        {
            throw new \Exception(sprintf('Transport service %s is not mapped to any transporter!', $transportService));
        }

        return $map[$transportService];
    }

    /**
     * @param Package[] $packages
     * @return string
     * @throws \Exception
     */
    public function parcel(array $packages)
    {
        foreach($packages AS $package)
        {
            //Get transporter from package
            $transporter = $this->transportServiceToTransporter($package);
            //Get transporter class
            $iTransporter = $this->getTransporter($transporter);

            //Get next unique ID for package from series, this action generates PackageNumber too
            $seriesNumberId = $this->botStorage->getNextSeriesNumberId($transporter, $package->getTransportService(), $this->sender['senderId']);
            $package->setSeriesNumberId($seriesNumberId);

            //Test if we can create Transporter package
            $transporterPackage = $iTransporter->packageBotPackageToTransporterPackage($package);

            //If we get here, everything went ok, so we can save package into storage
            $this->botStorage->savePackage($transporter, $package->getOrderId(), $package->getSeriesNumberId(), $transporterPackage->getPackageNumber(), $package);
        }
    }

    /**
     * @param array $packages
     * @param int $decomposition
     * @return string
     * @throws WrongDeliveryDataException
     * @throws \Exception
     */
    public function getLabels(array $packages, $decomposition = LabelDecomposition::QUARTER)
    {
        if (!in_array($decomposition, LabelDecomposition::$list)) {
            throw new WrongDeliveryDataException(sprintf('unknown $decomposition ony %s are allowed', implode(', ', LabelDecomposition::$list)));
        }

        $packageNumbers = [];
        foreach($packages AS $package)
        {
            //Get transporter from package
            $transporter = $this->transportServiceToTransporter($package);
            //Get transporter class
            $iTransporter = $this->getTransporter($transporter);

            //Test if we can create Transporter package
            $transporterPackage = $iTransporter->packageBotPackageToTransporterPackage($package);

            $packageNumbers[] = $transporterPackage->getPackageNumber();
        }

        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Adam Schubert');
        $pdf->SetTitle(sprintf('Package bot Label %s', implode(', ', $packageNumbers)));
        $pdf->SetSubject(sprintf('Package bot Label %s', implode(', ', $packageNumbers)));
        $pdf->SetKeywords('Package bot');
        $pdf->SetFont('freeserif');
        $pdf->setFontSubsetting(true);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $quarterPosition = LabelPosition::TOP_LEFT;
        /** @var Package $package */
        foreach ($packages AS $package) {
            $transporter = $this->transportServiceToTransporter($package);
            //Get transporter class
            $iTransporter = $this->getTransporter($transporter);

            switch ($decomposition) {
                case LabelDecomposition::FULL:
                    $pdf->AddPage();
                    $pdf = $iTransporter->doGenerateLabelFull($pdf, $package);
                    break;
                case LabelDecomposition::QUARTER:
                    if ($quarterPosition > LabelPosition::BOTTOM_RIGHT) {
                        $quarterPosition = LabelPosition::TOP_LEFT;
                    }
                    if ($quarterPosition == LabelPosition::TOP_LEFT) {
                        $pdf->AddPage();
                    }
                    $pdf = $iTransporter->doGenerateLabelQuarter($pdf, $package, $quarterPosition);
                    $quarterPosition++;
                    break;
            }
        }
        return $pdf->Output(null, 'S');
    }
}