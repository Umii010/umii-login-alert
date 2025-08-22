<?php

return [
    'include_location' => true,
    'notify_via' => ['mail'],
    'location_resolver' => function (string $ip) {
        return 'Unknown';
    },
];
