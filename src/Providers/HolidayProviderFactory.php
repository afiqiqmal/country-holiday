<?php

namespace Holiday\Providers;

use Holiday\Contracts\HolidayProviderInterface;
use Holiday\Country\AnonymousHolidayProvider;
use Holiday\Country\MalaysiaHolidayProvider;
use Holiday\Exception\CountryNotSupportedException;
use Symfony\Component\BrowserKit\HttpBrowser;

class HolidayProviderFactory
{
    private static array $providers = [
        'malaysia' => MalaysiaHolidayProvider::class,

        // Add more country providers here
        'anonymous' => AnonymousHolidayProvider::class,
    ];

    /**
     * Create a holiday provider for a specific country.
     *
     * @param string $country Country name
     * @param HttpBrowser|null $client HTTP client
     * @return HolidayProviderInterface Holiday provider
     */
    public static function create(string $country, ?HttpBrowser $client = null): HolidayProviderInterface
    {
        $normalizedCountry = ! in_array($country, array_keys(self::$providers)) ?
            'anonymous' : strtolower($country);

        if (!isset(self::$providers[$normalizedCountry])) {
            throw new CountryNotSupportedException(
                "Holiday provider not found for country: {$country}"
            );
        }

        $providerClass = self::$providers[$normalizedCountry];
        $provider = new $providerClass($client);

        if ($normalizedCountry === 'anonymous') {
            $provider->setCountrySlug($country);
        }

        return $provider;
    }
}