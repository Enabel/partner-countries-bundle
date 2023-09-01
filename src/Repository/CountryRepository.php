<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Enabel\PartnerCountriesBundle\Entity\Country;

class CountryRepository extends ServiceEntityRepository
{
    public function add(Country $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Country $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return array<string, Country>
     */
    public function getCountryCodes(): array
    {
        $countries = [];

        foreach ($this->findAll() as $country) {
            /** @var Country $country */
            $countries[$country->getAlpha2code()] = $country;
        }

        return $countries;
    }
}
