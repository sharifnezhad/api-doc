<?php
return [
    'url' => 'doc',
    'title' => 'sharif doc',
    'description' => 'sharif web only',
    'version' => '1.0.0',
    'license' => null,
    'logo' => 'https://www.logoko.com.cn/uploadfile/icon_case/201810/5bd03463d436b.png',
    'color' => null,
    'output' => 'storage',
    'hide_download_button' => false,
    'hide_try_it' => true,
    'servers' => [
        [
            'url' => env('APP_URL'),
            'description' => 'Documentation generator server.',
        ],
        [
            'url' => 'http://test.example.com',
            'description' => 'Test server.',
        ],
    ],
    'security' => [
        'BearerAuth' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
            'in' => 'header'
        ],
        "oauth2" => [
            "type" => "oauth2",
            "flows" => [
                "implicit" => [
                    "authorizationUrl" => "https=>//example.com/oauth/authorize",
                    "scopes" => [
                        "read" => "Grants read access to resources",
                        "write" => "Grants write access to resources",
                        "admin" => "Grants administrative access to resources"
                    ],
                ],
            ],
        ],
        "apiKey" => [
            "type" => "apiKey",
            "name" => "X-API-Key",
            "in" => "header"
        ],
        "basicAuth" => [
            "type" => "http",
            "scheme" => "basic"
        ]
    ],

    'headers' => [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJod',
    ],

];
