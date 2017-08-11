# This is packageBot!

## Install by

```bash
composer require salamek/packagebot-local dev-master
```

## Usage

Add this to your config neon

```yaml
extensions:
  packageBot: Extensions\PackageBot\DI\PackageBotExtension

packageBot:
  sender:
    name: CALBUCO s.r.o.
    www: grizly.cz
    street: Větrná
    streetNumber: 378/6
    zipCode: 783 36
    cityPart: Křelov-Břuchotín
    city: Křelov
    countryCode: cz
    email: info@grizly.cz
    phone: 725473077
  transporters:
    czechPost:
      enabled: false
      id: 9567
      postOfficeZipCode: 770 72
      username:
      password:

    professionalParcelLogistic:
      enabled: true
      username:
      password:
      customerId: 1860940
      depoCode: 09

    ulozenka:
      enabled: false
      username:
      password:
    zasilkovna:
      enabled: false
      apiPassword: ''
      apiKey: ''
```
