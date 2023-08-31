<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Tests\Entity;

use Enabel\PartnerCountriesBundle\Entity\Country;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Intl;

class CountryTest extends TestCase
{
    public function testSetterGetter(): void
    {
        // Get data
        $data = $this->getData();

        // Create entity with data
        $entity = $this->createEntityWithData($data);

        // Test data
        $this->assertSame($entity->getId(), $data['id']);
        $this->assertSame($entity->getAlpha2code(), $data['alpha2code']);
        $this->assertSame($entity->isPartner(), $data['isPartner']);
        $this->assertSame($entity->getAlpha3code(), $data['alpha3code']);
        $this->assertSame($entity->getName(), $data['name']);
    }

    /**
     * @param array{
     *     id: int,
     *     alpha2code: string,
     *     isPartner: bool,
     * } $data
     */
    private function createEntityWithData(array $data): Country
    {
        // Create entity
        $entity = new Country();

        // Set data
        $entity->setId($data['id']);
        $entity->setAlpha2code($data['alpha2code']);
        $entity->setIsPartner($data['isPartner']);

        return $entity;
    }

    /**
     * @return array{
     *     id: int,
     *     alpha2code: string,
     *     isPartner: bool,
     *     alpha3code: string,
     *     name: string
     * }
     */
    private function getData(): array
    {
        return [
            'id' => 1,
            'alpha2code' => 'BE',
            'isPartner' => true,
            'alpha3code' => Countries::getAlpha3Code('BE'),
            'name' => Countries::getName('BE')
        ];
    }
}
