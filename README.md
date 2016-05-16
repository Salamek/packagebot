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
  transporters:
    czechPost:
      enabled: false
      id: 9567
      postOfficeZipCode: 770 72
      username:
      password:

    ppl:
      enabled: false
      username:
      password:

    ulozenka:
      enabled: false
      username:
      password:
```
