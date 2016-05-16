<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;


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
     * @param $packageId
     * @param $packageData
     * @param \DateTime|null $send
     * @return void
     */
    public function savePackage($transporter, $orderId, $packageId, $packageData, \DateTime $send = null)
    {
        $this->set(self::STORAGE_TABLE_PACKAGE.$transporter, $packageId, $packageData);
    }

    /**
     * @param $transporter
     * @return array
     */
    public function getUnSendPackages($transporter)
    {
        return $this->all(self::STORAGE_TABLE_PACKAGE.$transporter);
    }

    /**
     * @param $transporter
     * @param null $packageType
     * @param null $sender
     * @param null $year
     * @return int|null
     */
    public function getNextPackageId($transporter, $packageType = null, $sender = null, $year = null)
    {
        $key = implode('-', [$packageType, $sender, $year]);

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
     * @param $packageId
     * @param \DateTime $date
     * @return void
     */
    public function setSend($transporter, $packageId, \DateTime $date)
    {
        $this->set(self::STORAGE_TABLE_PACKAGE.$transporter, $packageId, null);
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
     * @param $packageId
     * @return null
     */
    public function getPackageByPackageId($transporter, $packageId)
    {
        return $this->get(self::STORAGE_TABLE_PACKAGE.$transporter, $packageId);
    }

    /**
     * @param $transporter
     * @param $packageId
     * @param $label
     * @return string
     */
    public function savePackageLabel($transporter, $packageId, $label)
    {
        $table = self::STORAGE_TABLE_LABEL.$transporter;
        $this->checkTable($table);

        $path = $this->dirStorage.'/'.$table.'/'.$packageId.'.pdf';
        file_put_contents($path, $label);
        return $path;
    }
}