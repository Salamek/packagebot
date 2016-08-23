<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;


use Salamek\PackageBot\Model\Package;

class PackageBotFileStorage implements IPackageBotStorage
{
    private $dirStorage;

    const STORAGE_TABLE_PACKAGE = 'packages';
    const STORAGE_TABLE_LABEL = 'labels';
    const STORAGE_TABLE_PACKAGE_ID = 'PackageId';

    /**
     * CzechPostFileStorage constructor.
     * @param null $dirPath
     */
    public function __construct($dirPath = null)
    {
        if (is_null($dirPath))
        {
            $dirPath = __DIR__.'/'.str_replace('\\', '_', __CLASS__);
        }

        if (!is_dir($dirPath))
        {
            mkdir($dirPath, 0777, true);
        }

        $this->dirStorage = $dirPath;
    }

    /**
     * @param $table
     * @return string
     */
    private function checkTable($table)
    {
        $dirPath = $this->dirStorage.'/'.$table;
        if (!is_dir($dirPath))
        {
            mkdir($dirPath, 0777, true);
        }

        return $dirPath;
    }

    /**
     * @param $table
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($table, $key, $default = null)
    {
        $this->checkTable($table);
        if (!is_file($this->dirStorage.'/'.$table.'/'.$key.'.json'))
        {
            return $default;
        }

        return unserialize(file_get_contents($this->dirStorage.'/'.$table.'/'.$key.'.json'));
    }

    /**
     * @param $table
     * @param $key
     * @param null $value
     * @return null
     */
    public function set($table, $key, $value = null)
    {
        $this->checkTable($table);
        if (is_null($value) && $this->get($table, $key))
        {
            unlink($this->dirStorage.'/'.$table.'/'.$key.'.json');
            return null;
        }

        $path = $this->dirStorage.'/'.$table.'/'.$key.'.json';
        file_put_contents($path, serialize($value));
    }

    /**
     * @param $table
     * @return array
     */
    public function all($table)
    {
        $this->checkTable($table);
        $return = [];
        $files = array_diff(scandir($this->dirStorage.'/'.$table), array('..', '.'));
        foreach($files AS $file)
        {
            $return[] = unserialize(file_get_contents($this->dirStorage.'/'.$table.'/'.$file));
        }
        return $return;
    }

    /**
     * @param $transporter
     * @param $orderId
     * @param $seriesId
     * @param $packageNumber
     * @param Package $packageData
     * @param \DateTimeInterface|null $send
     * @return void
     */
    public function savePackage($transporter, $orderId, $seriesId, $packageNumber, Package $packageData, \DateTimeInterface $send = null)
    {
        $this->set(self::STORAGE_TABLE_PACKAGE.$transporter, $seriesId, $packageData);
    }

    /**
     * @param $transporter
     * @return array
     */
    public function getUnSentPackages($transporter)
    {
        return $this->all(self::STORAGE_TABLE_PACKAGE.$transporter);
    }

    /**
     * @param $transporter
     * @param null $packageType
     * @param null $sender
     * @return int|null
     */
    public function getNextSeriesNumberId($transporter, $packageType = null, $sender = null)
    {
        $key = implode('-', [$packageType, $sender]);

        $value = $this->get(self::STORAGE_TABLE_PACKAGE_ID.$transporter, $key);

        if (is_null($value))
        {
            $value = 1;
        }
        else
        {
            $value++;
        }

        $this->set(self::STORAGE_TABLE_PACKAGE_ID.$transporter, $key, $value);

        return $value;
    }

    /**
     * @param $transporter
     * @param Package[] $packages
     * @param \DateTimeInterface $date
     * @return void
     */
    public function setSendPackages($transporter, array $packages, \DateTimeInterface $date)
    {
        foreach($packages AS $package)
        {
            $this->set(self::STORAGE_TABLE_PACKAGE.$transporter, $package->get, null);
        }
    }

    /**
     * @param $transporter
     * @param $orderId
     * @throws \Exception
     * @return void
     */
    public function getPackageByOrderId($transporter, $orderId)
    {
        // TODO: Implement getPackageByOrderId() method.
        throw new \Exception(__CLASS__.' is unable to do that');
    }

    /**
     * @param $transporter
     * @param $packageNumber
     * @throws \Exception
     * @return null
     */
    public function getPackageByPackageNumber($transporter, $packageNumber)
    {
        // TODO: Implement getPackageByPackageNumber() method.
        throw new \Exception(__CLASS__.' is unable to do that');
    }

    /**
     * @param $transporter
     * @param $seriesNumberId
     * @return null
     */
    public function getPackageBySeriesNumberId($transporter, $seriesNumberId)
    {
        return $this->get(self::STORAGE_TABLE_PACKAGE.$transporter, $seriesNumberId);
    }
}