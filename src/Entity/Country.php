<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Enabel\PartnerCountriesBundle\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Countries;

#[UniqueEntity('alpha2code', message: 'This country already exists')]
#[ORM\MappedSuperclass(repositoryClass: CountryRepository::class)]
#[ORM\Table(options: ['comment' => 'Table of countries'])]
class Country
{
    public const PARTNER_COUNTRIES = [
        'BE', 'BF', 'BI', 'BJ', 'CD', 'CF', 'CI', 'GN',
        'JO', 'MA', 'ML', 'MR', 'MZ', 'NE', 'PS', 'RW', 'SN', 'TZ', 'UG'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['comment' => 'Unique identifier of the country'])]
    protected int $id;

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

    public function setId(int $id): self
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
        return Countries::getAlpha3Code($this->getAlpha2code());
    }

    public function getName(): string
    {
        return Countries::getName($this->alpha2code);
    }
}
