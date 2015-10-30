<?php

return [
    'max_attempts'      => [
        'required' => true,
        'type'     => 'anomaly.field_type.integer',
        'config'   => [
            'min'           => 3,
            'default_value' => 5
        ]
    ],
    'throttle_interval' => [
        'required' => true,
        'type'     => 'anomaly.field_type.integer',
        'config'   => [
            'min'           => 1,
            'default_value' => 1
        ]
    ],
    'lockout_interval'  => [
        'required' => true,
        'type'     => 'anomaly.field_type.integer',
        'config'   => [
            'min'           => 1,
            'default_value' => 1
        ]
    ],
    'error_message'     => [
        'required' => true,
        'type'     => 'anomaly.field_type.text',
        'config'   => [
            'default_value' => 'streams::message.429'
        ]
    ]
];
