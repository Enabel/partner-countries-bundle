# Manage partner countries in your Easyadmin dashboard

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
