<?php

return [
    'channels' => ['mail'],
    'only_new_devices' => false,
    'include_location' => true,
    'location_resolver' => Umii\LoginAlert\Resolvers\IpApiLocationResolver::class,
];
