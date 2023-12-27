<?php

use CRM_ApiLog_ExtensionUtil as E;

return [
  'api_log_entity_filter' => [
    'group_name' => 'ApiLog Config',
    'group' => 'api_log',
    'name' => 'api_log_entity_filter',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => [],
    'description' => E::ts('Filter log records based on Entity values'),
    'html_type' => 'text',
  ],
  'api_log_action_filter' => [
    'group_name' => 'ApiLog Config',
    'group' => 'api_log',
    'name' => 'api_log_action_filter',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => [],
    'description' => E::ts('Filter log records based on Action values'),
    'html_type' => 'text',
  ],
  'api_log_request_filter' => [
    'group_name' => 'ApiLog Config',
    'group' => 'api_log',
    'name' => 'api_log_request_filter',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => [],
    'description' => E::ts('Filter log records based on Request values'),
    'html_type' => 'text',
  ],
  'api_log_response_filter' => [
    'group_name' => 'ApiLog Config',
    'group' => 'api_log',
    'name' => 'api_log_response_filter',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => [],
    'description' => E::ts('Filter log records based on Response values'),
    'html_type' => 'text',
  ],
  'api_log_success_filter' => [
    'group_name' => 'ApiLog Config',
    'group' => 'api_log',
    'name' => 'api_log_success_filter',
    'type' => 'String',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'default' => [],
    'description' => E::ts('Filter log records based on Success values'),
    'html_type' => 'Select',
    'html_attributes' => [
      'size' => 20,
      'class' => 'crm-select2',
    ],
    'multiple' => true,
  ],
];