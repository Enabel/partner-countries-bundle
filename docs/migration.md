# Migrating away from the bundle

This bundle is **deprecated** and will not receive further updates. What it provides (a `country`
table, a sync command, an EasyAdmin CRUD and two Twig filters) is small enough to live in your
application. This guide moves it into your project **without changing the database schema**, so no
data migration is needed.

The approach: copy the code into the `App\` namespace with the same class names, property names and
mapping options, so Doctrine sees exactly the same table and columns.

Do all the steps in a single branch: the application is only expected to boot again once the last
step is done.

> The snippets below assume the namespaces used in the installation guide
> (`App\Entity\Enabel\Country`, `App\Repository\Enabel\CountryRepository`,
> `App\Controller\Admin\Enabel\CountryCrudController`). Adapt them if your project differs.

## Step 1: Unregister the bundle

Start by switching the bundle off, while keeping the package installed so your classes that extend it
still load during the migration:

1. Delete `config/packages/enabel_partner_countries.yaml`: its options require classes extending the
   bundle ones, which stops being true in the next steps.
2. Remove the `enabel_partner_countries` import from `config/routes.yaml` (or `config/routes/`).
3. Remove `Enabel\PartnerCountriesBundle\EnabelPartnerCountriesBundle` from `config/bundles.php`. This
   also frees the `enabel:partner-countries:sync` command name for your own command.

## Step 2: Inline the entity

Replace your `src/Entity/Enabel/Country.php` (which extended the bundle one) with a standalone
entity. Keep the property names and the `options` untouched: they define the column names and
comments of the existing table.

```php
<?php

declare(strict_types=1);

namespace App\Entity\Enabel;

use App\Repository\Enabel\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Countries;

#[UniqueEntity('alpha2code', message: 'This country already exists')]
#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(options: ['comment' => 'Table of countries'])]
class Country implements \Stringable
{
    public const array PARTNER_COUNTRIES = [
        'BE', 'BF', 'BI', 'BJ', 'CD', 'CF', 'CI', 'GN',
        'JO', 'MA', 'ML', 'MR', 'MZ', 'NE', 'PS', 'RW', 'SN', 'TZ', 'UG',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['comment' => 'Unique identifier of the country'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 2, nullable: false, options: ['comment' => 'Code of the country (isoalpha2)'])]
    private string $alpha2code;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['comment' => 'Is this country a partner country?'])]
    private bool $isPartner = false;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getAlpha2code(): string
    {
        return $this->alpha2code;
    }

    public function setAlpha2code(string $alpha2code): static
    {
        $this->alpha2code = $alpha2code;

        return $this;
    }

    public function isPartner(): bool
    {
        return $this->isPartner;
    }

    public function setIsPartner(bool $isPartner): static
    {
        $this->isPartner = $isPartner;

        return $this;
    }

    public function getAlpha3code(): string
    {
        return Countries::getAlpha3Code($this->alpha2code);
    }

    public function getName(): string
    {
        return Countries::getName($this->alpha2code);
    }
}
```

Keep the public API identical for now (getters and setters): the rest of your code keeps working.
Modernise it (property hooks, readonly…) in a separate change once the bundle is gone.

## Step 3: Inline the repository

```php
<?php

declare(strict_types=1);

namespace App\Repository\Enabel;

use App\Entity\Enabel\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * @return array<string, Country>
     */
    public function getCountryCodes(): array
    {
        $countries = [];

        foreach ($this->findAll() as $country) {
            $countries[$country->getAlpha2code()] = $country;
        }

        return $countries;
    }
}
```

The bundle also had `add()` and `remove()` helpers. If your code calls them, either keep them or
replace the calls with `$entityManager->persist()` / `remove()` / `flush()`.

## Step 4: Copy the sync command

Only needed if you run `enabel:partner-countries:sync` (at deploy time, in a cron…). Keeping the same
command name means you don't have to touch your deploy scripts.

```php
<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Enabel\Country;
use App\Repository\Enabel\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Intl\Countries;

#[AsCommand(
    name: 'enabel:partner-countries:sync',
    description: 'Sync the data of the partner countries table',
)]
final readonly class SyncPartnerCountriesCommand
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CountryRepository $countryRepository,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $countries = $this->countryRepository->getCountryCodes();

        foreach (Countries::getCountryCodes() as $countryCode) {
            $country = $countries[$countryCode] ?? new Country()->setAlpha2code($countryCode);
            $country->setIsPartner(in_array($countryCode, Country::PARTNER_COUNTRIES, true));

            $this->entityManager->persist($country);
        }

        $this->entityManager->flush();

        $io->success('Data for the partner countries table has been sync successfully.');

        return Command::SUCCESS;
    }
}
```

> **Watch out:** the sync overwrites `isPartner` from `PARTNER_COUNTRIES`, so any change made in the
> admin is lost on the next sync. Now that the code is yours, pick one source of truth: either the
> constant (and make `isPartner` read-only in the admin) or the database (and stop resetting the flag
> in the command).

## Step 5: Inline the EasyAdmin CRUD controller

Your `CountryCrudController` extended the bundle's abstract one. Merge the parent configuration into
it:

```php
<?php

declare(strict_types=1);

namespace App\Controller\Admin\Enabel;

use App\Entity\Enabel\Country;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Country>
 */
class CountryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Country::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('enabel_partner_countries.admin.menu.country')
            ->setEntityLabelInPlural('enabel_partner_countries.admin.menu.countries')
            ->setPageTitle('index', '%entity_label_plural%')
            ->setPageTitle('edit', 'enabel_partner_countries.admin.title.country.edit')
            ->setPageTitle('detail', 'enabel_partner_countries.admin.title.country.detail')
            ->setSearchFields(['id', 'alpha2code'])
            ->setEntityPermission('ROLE_ADMIN_PARTNER_COUNTRIES')
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'enabel_partner_countries.admin.form.id')->onlyOnDetail();
        yield CountryField::new('alpha2code', 'enabel_partner_countries.admin.form.country');
        yield TextField::new('alpha2code', 'enabel_partner_countries.admin.form.alpha2code')->onlyOnDetail();
        yield TextField::new('alpha3code', 'enabel_partner_countries.admin.form.alpha3code')->onlyOnDetail();
        yield BooleanField::new('isPartner', 'enabel_partner_countries.admin.form.isPartner');
    }
}
```

In your dashboard, drop the `CountryTrait` and link the controller directly:

```diff
-use Enabel\PartnerCountriesBundle\Controller\Admin\CountryTrait;
+use App\Controller\Admin\Enabel\CountryCrudController;
 
 class DashboardController extends AbstractDashboardController
 {
-    use CountryTrait;
-
     public function configureMenuItems(): iterable
     {
-        yield from $this->countryMenuEntry();
+        yield MenuItem::linkTo(CountryCrudController::class, 'enabel_partner_countries.admin.menu.country', 'fa fa-earth-africa');
```

## Step 6: Copy the translations

Copy the keys you use from the bundle's
[`translations/messages.en.yaml`](https://github.com/Enabel/partner-countries-bundle/blob/main/translations/messages.en.yaml)
and [`messages.fr.yaml`](https://github.com/Enabel/partner-countries-bundle/blob/main/translations/messages.fr.yaml)
into your `translations/messages.*.yaml`, or replace the keys in the controller with your own labels.

## Step 7: Copy the Twig filters (if used)

Search your templates for `country_icon`. If you use the filters, add this extension (the flags come
from [flag-icons](https://flagicons.lipis.dev/), which your project already loads):

```php
<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Enabel\Country;
use Symfony\Component\Intl\Countries;
use Twig\Attribute\AsTwigFilter;

final class CountryExtension
{
    #[AsTwigFilter('country_icon', isSafe: ['html'])]
    public function icon(Country|string $country): string
    {
        return sprintf('<span class="fi fi-%s"></span>', strtolower($this->alpha2code($country)));
    }

    #[AsTwigFilter('country_icon_with_name', isSafe: ['html'])]
    public function iconWithName(Country|string $country): string
    {
        $code = $this->alpha2code($country);

        return $this->icon($code) . '&nbsp;' . htmlspecialchars(Countries::getName($code));
    }

    private function alpha2code(Country|string $country): string
    {
        if ($country instanceof Country) {
            return $country->getAlpha2code();
        }

        return match (strlen($country)) {
            2 => strtoupper($country),
            3 => Countries::getAlpha2Code(strtoupper($country)),
            default => throw new \InvalidArgumentException('The country code must be the alpha2code or the alpha3code.'),
        };
    }
}
```

`#[AsTwigFilter]` requires Twig 3.21+ and Symfony 7.3+. With autoconfiguration on (the default), no
service configuration is needed.

## Step 8: Remove the package

1. Uninstall it:

   ```bash
   composer remove enabel/partner-countries-bundle
   ```

2. Search for leftovers: `grep -rn "PartnerCountriesBundle\|enabel_partner_countries" src config templates`.
   Only the translation keys (`enabel_partner_countries.admin.*`) should remain. The Twig global
   `enabel_partner_countries` went away with the bundle in Step 1.

## Step 9: Check the schema did not change

```bash
bin/console doctrine:schema:validate
bin/console make:migration
```

`doctrine:schema:validate` must report the mapping as in sync with the database, and `make:migration`
must find nothing to generate. If a migration shows up, a column name or option differs from the
bundle mapping: fix the entity instead of running that migration.
