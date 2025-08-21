<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Channels
    |--------------------------------------------------------------------------
    | Supported: "mail", "vonage" (if installed)
    */
    'channels' => ['mail'],

    /*
    |--------------------------------------------------------------------------
    | Only new devices
    |--------------------------------------------------------------------------
    | When true, alerts are only sent when a new device/IP (fingerprint) is seen.
    */
    'only_new_devices' => true,

    /*
    |--------------------------------------------------------------------------
    | Include location
    |--------------------------------------------------------------------------
    | When true, we will try to enrich the alert with a textual location.
    | Provide a callable resolver in your app service provider:
    |
    | config(['login-alert.location_resolver' => function (string $ip) {
    |     // return "City, Country" without making network calls here
    |     return null;
    | }]);
    */
    'include_location' => false,

    // Do not edit: runtime hook
    'location_resolver' => null,
];
