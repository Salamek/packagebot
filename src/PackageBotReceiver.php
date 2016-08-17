<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;

class PackageBotReceiver
{
    /** @var integer */
    private $id;

    /** @var  string */
    private $firstName;

    /** @var  string */
    private $lastName;

    /** @var  string */
    private $email;

    /** @var  string */
    private $phone;

    /** @var  string */
    private $www;

    /** @var  string */
    private $street;

    /** @var  string */
    private $streetNumber;

    /** @var  string */
    private $zipCode;

    /** @var  string */
    private $city;

    /** @var  string */
    private $cityPart;

    /** @var  string */
    private $state;

    /** @var  string */
    private $company;

    /** @var null|string */
    private $companyId = null;

    /** @var null|string */
    private $companyVatId = null;

    /**
     * PackageBotReceiver constructor.
     * @param $company
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $phone
     * @param $www
     * @param $street
     * @param $streetNumber
     * @param $zipCode
     * @param $city
     * @param $cityPart
     * @param $state
     */
    public function __construct(
        $company,
        $firstName,
        $lastName,
        $email,
        $phone,
        $www,
        $street,
        $streetNumber,
        $zipCode,
        $city,
        $cityPart,
        $state
    ) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setCompany($company);
        $this->setEmail($email);
        $this->setPhone($phone);
        $this->setWww($www);
        $this->setStreet($street);
        $this->setStreetNumber($streetNumber);
        $this->setZipCode($zipCode);
        $this->setCity($city);
        $this->setCityPart($cityPart);
        $this->setState($state);
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @param string $www
     */
    public function setWww($www)
    {
        $this->www = $www;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @param string $cityPart
     */
    public function setCityPart($cityPart)
    {
        $this->cityPart = $cityPart;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $state
     * @throws WrongDeliveryDataException
     */
    public function setState($state)
    {
        if (!in_array($state, PackageBotDial::$supportedCountryCodes))
        {
            throw new WrongDeliveryDataException('Unsupported country code, supported codes are '.implode(' ,', PackageBotDial::$supportedCountryCodes));
        }

        $this->state = $state;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @param string $companyVatId
     */
    public function setCompanyVatId($companyVatId)
    {
        $this->companyVatId = $companyVatId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCityPart()
    {
        return $this->cityPart;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getWww()
    {
        return $this->www;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return null|string
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @return null|string
     */
    public function getCompanyVatId()
    {
        return $this->companyVatId;
    }
}