<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

abstract class CountryCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('enabel_partner_countries.admin.menu.country')
            ->setEntityLabelInPlural('enabel_partner_countries.admin.menu.countries')
            ->setPageTitle('index', '%entity_label_plural%')
            ->setPageTitle('new', 'enabel_partner_countries.admin.title.country.new')
            ->setPageTitle('edit', 'enabel_partner_countries.admin.title.country.edit')
            ->setPageTitle('detail', 'enabel_partner_countries.admin.title.country.detail')
            ->setSearchFields(['id', 'alpha2code'])
            ->setEntityPermission('ROLE_ADMIN_PARTNER_COUNTRIES')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id', 'enabel_partner_countries.admin.form.id');
        $country = CountryField::new('alpha2code', 'enabel_partner_countries.admin.form.country');
        $code2 = TextField::new('alpha2code', 'enabel_partner_countries.admin.form.alpha2code');
        $code3 = TextField::new('alpha3code', 'enabel_partner_countries.admin.form.alpha3code');
        $isPartner = BooleanField::new('isPartner', 'enabel_partner_countries.admin.form.isPartner');

        return [$id->onlyOnDetail(), $country, $code2->onlyOnDetail(), $code3->onlyOnDetail(), $isPartner];
    }
}
