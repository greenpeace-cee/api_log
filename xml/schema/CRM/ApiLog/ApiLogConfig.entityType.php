<?php
// This file declares a new entity type. For more details, see "hook_civicrm_entityTypes" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
return [
  [
    'name' => 'ApiLogConfig',
    'class' => 'CRM_ApiLog_DAO_ApiLogConfig',
    'table' => 'civicrm_api_log_config',
  ],
];
