<?php

require_once 'api_log.civix.php';

// phpcs:disable
use Civi\Utils\Settings;
use CRM_ApiLog_ExtensionUtil as E;

// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function api_log_civicrm_config(&$config): void {
  _api_log_civix_civicrm_config($config);

  if (Settings::getIsApiLoggerEnabled()) {
    Civi::dispatcher()->addListener('civi.api.exception', ['CRM_ApiLog_ApiLogService', 'onApiException'], -100);
    Civi::dispatcher()->addListener('civi.api.respond', ['CRM_ApiLog_ApiLogService', 'onApiRespond'], -100);
  }
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

function api_log_civicrm_navigationMenu(&$menu) {
  _api_log_civix_insert_navigation_menu($menu, 'Administer/System Settings', array(
    'label' => E::ts('API Logs'),
    'name' => 'api_log',
    'url' => null,
    'permission' => 'access CiviCRM',
    'operator' => null,
    'separator' => 0,
    'icon' => 'crm-i fa-cog',
  ));

  _api_log_civix_insert_navigation_menu($menu, 'Administer/System Settings/api_log', array(
    'label' => E::ts('API Log Settings'),
    'name' => 'api_log_api_log_settings',
    'url' => 'civicrm/api-log/api-log-settings',
    'permission' => 'access CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ));

  _api_log_civix_insert_navigation_menu($menu, 'Administer/System Settings/api_log', array(
    'label' => E::ts('API Log Configurations'),
    'name' => 'api_log_api_log_configs',
    'url' => 'civicrm/api-log',
    'permission' => 'access CiviCRM',
    'operator' => 'OR',
    'separator' => 0,
  ));

  _api_log_civix_navigationMenu($menu);

}