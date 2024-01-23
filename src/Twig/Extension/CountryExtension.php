<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Twig\Extension;

use Enabel\PartnerCountriesBundle\Entity\Country;
use Symfony\Component\Intl\Countries;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CountryExtension extends AbstractExtension
{
    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('country_icon', [$this, 'getIcon'], ['is_safe' => ['html']]),
            new TwigFilter('country_icon_with_name', [$this, 'getIconWithName'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string|Country $country
     */
    public function getIconWithName($country): string
    {
        $country = $this->extractCountryObject($country);

        return $this->getIcon($country) . '&nbsp;' . $country->getName();
    }

    /**
     * @param string|Country $country
     */
    public function getIcon($country): string
    {
        $country = $this->extractCountryObject($country);

        return $this->getFlagIcon($country->getAlpha2code());
    }

    /**
     * @param string|Country $country
     */
    private function extractCountryObject($country): Country
    {
        if ($country instanceof Country) {
            return $country;
        }

        if (is_string($country)) {
            if (strlen($country) !== 2 && strlen($country) !== 3) {
                throw new \InvalidArgumentException('The country code must be the alpha2code or the alpha3code.');
            }

            if (strlen($country) === 3) {
                $countryCode = Countries::getAlpha2Code($country);
                return (new Country())->setAlpha2code($countryCode);
            }
        }

        throw new \InvalidArgumentException('The country must be a string or an instance of Country.');
    }

    private function getFlagIcon(string $countryCode): string
    {
        return '<span class="fi fi-' . strtolower($countryCode) . '"></span>';
    }
}
