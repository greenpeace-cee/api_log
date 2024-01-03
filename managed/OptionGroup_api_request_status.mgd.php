<?php

use CRM_ApiLog_ExtensionUtil as E;

return [
  [
    'name' => 'OptionGroup_api_request_status',
    'entity' => 'OptionGroup',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'api_request_status',
        'title' => E::ts('API Request Type'),
        'description' => NULL,
        'data_type' => NULL,
        'is_reserved' => TRUE,
        'is_active' => TRUE,
        'is_locked' => FALSE,
      ],
      'match' => ['name'],
    ],
  ],
];