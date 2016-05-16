<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;


class PackageBotParcelInfo
{
    private $packageLabel;
    private $packageId;

    public function __construct($packageId, $packageLabel)
    {
        $this->packageId = $packageId;
        $this->packageLabel = $packageLabel;
    }

    public function getPackageId()
    {
        return $this->packageId;
    }

    public function getPackageLabel()
    {
        return $this->packageLabel;
    }
}