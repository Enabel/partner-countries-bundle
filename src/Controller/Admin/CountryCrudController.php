<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

abstract class CountryCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('enabel_partner_countries.admin.menu.country')
            ->setEntityLabelInPlural('enabel_partner_countries.admin.menu.countries')
            ->setPageTitle('index', '%entity_label_plural%')
            ->setPageTitle('new', 'enabel_partner_countries.admin.menu.new_country')
            ->setPageTitle('edit', 'enabel_partner_countries.admin.menu.edit_country')
            ->setPageTitle('detail', 'enabel_partner_countries.admin.menu.country_detail')
            ->setSearchFields(['id', 'code', 'name'])
            ->setEntityPermission('ROLE_ADMIN_PARTNER_COUNTRIES')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id', 'enabel_partner_countries.admin.form.id');
        $country = CountryField::new('code', 'enabel_partner_countries.admin.form.country');
        $isPartner = BooleanField::new('isPartner', 'enabel_partner_countries.admin.form.is_partner');

        return [$id->onlyOnDetail(), $country, $isPartner];
    }
}
