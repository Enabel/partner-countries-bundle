# Manage partner countries in your Easyadmin dashboard

The menu entry links to your CRUD controller, so make sure the `country_admin_controller` option
points to your own controller extending the bundle one (see [installation](index.md)):

```yaml
# config/packages/enabel_partner_countries.yaml
enabel_partner_countries:
  country_admin_controller: 'App\Controller\Admin\Enabel\CountryCrudController'
```

Go to your dashboard controller, example : `src/Controller/Admin/DashboardController.php`

```php
<?php

namespace App\Controller\Admin;

...
use Enabel\PartnerCountriesBundle\Controller\Admin\CountryTrait;

class DashboardController extends AbstractDashboardController
{
    ...
    use CountryTrait;

    ...
    public function configureMenuItems(): iterable
    {
        ...
        yield from $this->countryMenuEntry();

        ...
```

This extra menu entry will allow you to manage your partner countries in your Easyadmin dashboard.
You need to have the permission `ROLE_ADMIN_PARTNER_COUNTRIES` to access this page. 
