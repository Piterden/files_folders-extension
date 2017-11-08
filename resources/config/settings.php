<?php

use Defr\FilesFoldersExtension\Support\Handler\StreamsHaveFiles;

return [
    'enabled_streams' => [
        'type'   => 'anomaly.field_type.checkboxes',
        'config' => [
            'handler' => StreamsHaveFiles::class . '@handle',
        ],
    ],
];
