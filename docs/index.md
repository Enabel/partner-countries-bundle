# Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

## Installation with Symfony Flex

Add our recipes endpoint

```json
{
  "extra": {
    "symfony": {
      "endpoint": [
        "https://api.github.com/repos/Enabel/recipes/contents/index.json?ref=flex/main",        
        "flex://defaults"
      ],
      "allow-contrib": true
    }
  }
}
```

**Don't forget to run `compose update` as you have just modified his configuration.**

Install with composer

```bash
composer require enabel/partner-countries-bundle
```

### Setup database

```bash
bin/console make:migration
bin/console doctrine:migration:migrate
```

## Installation without Symfony Flex


### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
composer require enabel/partner-countries-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Enabel\PartnerCountriesBundle\EnabelPartnerCountriesBundle::class => ['all' => true],
];
```

### Step 3: Import routing configuration

enable the routes by adding it to the list of registered routes
in the `config/routes.yaml` file of your project:

```yaml
# config/routes.yaml

enabel_partner_countries:
  resource: "@EnabelPartnerCountriesBundle/config/routes.yaml"
```

### Step 4: Create the configuration

Create a file `/config/packages/enabel_partner_countries.yaml` with this content:

```yaml
enabel_partner_countries:
  country_class: 'App\Entity\Enabel\Country'
  country_repository: 'App\Repository\Enabel\CountryRepository'
```

### Step 5: Create entity & repository

Create a entity and repository that extends the bundle one.

The Country entity `/src/Entity/Enabel/Country.php`
```php
<?php

declare(strict_types=1);

namespace App\Entity\Enabel;

use App\Repository\Enabel\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Enabel\PartnerCountriesBundle\Entity\Country as BaseCountry;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country extends BaseCountry
{
}
```

The according repository `/src/Repository/Enabel/CountryRepository.php`
```php
<?php

declare(strict_types=1);

namespace App\Repository\Enabel;

use App\Entity\Enabel\Country;
use Doctrine\Persistence\ManagerRegistry;
use Enabel\PartnerCountriesBundle\Repository\CountryRepository as BaseCountryRepository;

/**
 * @method Country|null   find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null   findOneBy(array $criteria, array $orderBy = null)
 * @method array<Country> findAll()
 * @method array<Country> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends BaseCountryRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }
}
```

### Step 6: Create the admin crud controller

Create a easyadmin crud controller that extends the bundle one.

To manage partner countries `/src/Controller/Admin/Enabel/CountryCrudController.php`

```php
<?php

declare(strict_types=1);

namespace App\Controller\Admin\Enabel;

use App\Entity\Enabel\Country;
use Enabel\PartnerCountriesBundle\Controller\Admin\CountryCrudController as BaseCountryCrudController;

class CountryCrudController extends BaseCountryCrudController
{
    public static function getEntityFqcn(): string
    {
        return Country::class;
    }
}
```

### Step 7: Setup the database

```bash
bin/console make:migration
bin/console doctrine:migration:migrate
```

## Usage

### Easyadmin

To manage partner countries in your Easyadmin dashboard follow [these instructions](easyadmin.md)

### Command

This bundle come with a bunch of commands, [here](command.md) is the documentation

### Twig

This bundle come with a bunch of twig filters, [here](twig.md) is the documentation
