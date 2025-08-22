<?php

namespace Umii\LoginAlert\Resolvers;

use GuzzleHttp\Client;

class IpApiLocationResolver
{
    public function __invoke(string $ip): string
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Localhost';
        }

        try {
            $client = new Client([ 'timeout' => 2.5 ]);
            $resp = $client->get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city");
            $data = json_decode((string) $resp->getBody(), true);

            if (is_array($data) && ($data['status'] ?? null) === 'success') {
                $city = $data['city'] ?? null;
                $region = $data['regionName'] ?? null;
                $country = $data['country'] ?? null;
                $parts = array_filter([$city, $region, $country]);
                return $parts ? implode(', ', $parts) : 'Unknown';
            }
        } catch (\Throwable $e) {}

        return 'Unknown';
    }
}
