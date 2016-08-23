<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Storage;


use Salamek\PackageBot\ISeriesNumberStorage;

class PackageBotSeriesNumberStorage extends FileStorage implements ISeriesNumberStorage
{
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
}