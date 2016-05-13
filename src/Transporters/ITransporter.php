<?php
namespace Extensions\PackageBot\Transporters;
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface ITransporter
{
    public function __construct(array $configuration);

    public function doParcel($firstName, $lastName, $description, $street, $streetNumber, $streetNumberSecond, $city, $cityPart, $postalCode, $state, $bankIdentifier, $email, $phone, $itemsPrice);
}