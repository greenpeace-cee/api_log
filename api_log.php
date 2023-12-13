<?php

require_once 'api_log.civix.php';
// phpcs:disable
use CRM_ApiLog_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function api_log_civicrm_config(&$config): void {
  _api_log_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function api_log_civicrm_install(): void {
  _api_log_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function api_log_civicrm_enable(): void {
  _api_log_civix_civicrm_enable();
}
