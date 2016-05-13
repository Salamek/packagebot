<?php
namespace Extensions\PackageBot\Transporters;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class CzechPost implements ITransporter
{
    private $username;
    private $password;

    private $newListFormUrl = 'https://www.postaonline.cz/podanionline/rucVstupZasilka.action';


    public function __construct(array $configuration)
    {
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];


        $api = new CzechPostApi($this->username, $this->password);
        
    }

    public function doParcel($firstName, $lastName, $description, $street, $streetNumber, $streetNumberSecond, $city, $cityPart, $postalCode, $state, $bankIdentifier, $email, $phone, $itemsPrice)
    {
        // TODO: Implement doParcel() method.
    }
}