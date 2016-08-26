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
use Salamek\PackageBot\Model\SendPackageResult;
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

    /** @var IPackageStorage */
    private $packageStorage;

    /** @var ISeriesNumberStorage */
    private $seriesNumberStorage;

    /** @var Nette\Caching\Cache */
    private $cache;

    /**
     * PackageBot constructor.
     * @param Nette\Caching\IStorage $cacheStorage
     * @param array $transporters
     * @param array $sender
     * @param IPackageStorage $packageStorage
     * @param ISeriesNumberStorage $seriesNumberStorage
     * @param string $tempDir
     */
    public function __construct(Nette\Caching\IStorage $cacheStorage, array $transporters, array $sender, IPackageStorage $packageStorage, ISeriesNumberStorage $seriesNumberStorage, $tempDir = null)
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
        $this->packageStorage = $packageStorage;
        $this->seriesNumberStorage = $seriesNumberStorage;
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
                $iTransporter = new $className($this->transporters[$transporter], $this->sender, $this->cookieJar);
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
                $unsentPackages = $this->packageStorage->getUnSentPackages($transporter);
                if (!empty($unsentPackages))
                {
                    /** @var SendPackageResult[] $sendPackagesResults */
                    $sendPackagesResults = $iTransporter->doSendPackages($unsentPackages);
                    $this->packageStorage->setSendPackages($transporter, $sendPackagesResults, new \DateTime());
                }
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
            TransportService::PPL_PARCEL_CZ_PRIVATE => Transporter::PPL,
            TransportService::PPL_PARCEL_CZ_PRIVATE_COD => Transporter::PPL,
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
            $transporter = $this->transportServiceToTransporter($package->getTransportService());

            $transporterConfig = $this->transporters[$transporter];

            //Get transporter class
            $iTransporter = $this->getTransporter($transporter);

            //Get next unique ID for package from series, this action generates PackageNumber too
            $seriesNumberInfo = $this->seriesNumberStorage->getNextSeriesNumberId($transporter, $package->getTransportService(), $transporterConfig['senderId']);
            $package->setSeriesNumberInfo($seriesNumberInfo);

            //Test if we can create Transporter package
            $transporterPackage = $iTransporter->packageBotPackageToTransporterPackage($package);

            //If we get here, everything went ok, so we can save package into storage
            $this->packageStorage->savePackage($transporter, $transporterPackage->getPackageNumber(), $package);
        }
    }

    /**
     * @param array $packages
     * @param int $decomposition
     * @param null|string $savePath
     * @return string
     * @throws WrongDeliveryDataException
     * @throws \Exception
     */
    public function getLabels(array $packages, $decomposition = LabelDecomposition::QUARTER, $savePath = null)
    {
        if (!in_array($decomposition, LabelDecomposition::$list)) {
            throw new WrongDeliveryDataException(sprintf('unknown $decomposition only %s are allowed', implode(', ', LabelDecomposition::$list)));
        }

        $packageNumbers = [];
        /** @var Package $package */
        foreach($packages AS $package)
        {
            //Get transporter from package
            $transporter = $this->transportServiceToTransporter($package->getTransportService());
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
            $transporter = $this->transportServiceToTransporter($package->getTransportService());
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


        if (is_null($savePath))
        {
            return $pdf->Output(null, 'S');
        }
        else
        {
            $fullPath = $savePath.'/'.implode('-', $packageNumbers).'.pdf';
            $pdf->Output($fullPath, 'F');
            return $fullPath;
        }
    }

    /**
     * @return array
     */
    public function getTransporters()
    {
        return $this->transporters;
    }

    /**
     * @return array
     */
    public function getSender()
    {
        return $this->sender;
    }
}