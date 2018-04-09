<?php

$currentDate = date('Y-m-d H:i:s');
$oneDayAgo = date('Y-m-d H:i:s', strtotime('-1 day'));

return [
    'valid' => [
        'user_id' => 1,
        'token' => 'valid_token',
        'created_at' => $currentDate,
        'consumed_at' => null,
    ],
    'expired' => [
        'user_id' => 1,
        'token' => 'expired_token',
        'created_at' => $oneDayAgo,
        'consumed_at' => null,
    ],
    'consumed' => [
        'user_id' => 1,
        'token' => 'consumed_token',
        'created_at' => $oneDayAgo,
        'consumed_at' => $oneDayAgo,
    ],
];