<?php
return [
    'spaces' => [
        'key' => $_ENV['DO_SPACES_KEY'] ?? '',
        'secret' => $_ENV['DO_SPACES_SECRET'] ?? '',
        'region' => $_ENV['DO_SPACES_REGION'] ?? 'nyc3',
        'bucket' => $_ENV['DO_SPACES_BUCKET'] ?? 'tvaz',
        'endpoint' => 'https://'.($_ENV['DO_SPACES_REGION'] ?? 'nyc3').'.digitaloceanspaces.com'
    ],
    'stream' => [
        'chunk_duration' => 10,
        'qualities' => ['720p', '480p', '360p']
    ]
];