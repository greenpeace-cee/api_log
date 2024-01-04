<?php

use Civi\API\Exception\UnauthorizedException;
use CRM_ApiLog_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_ApiLog_Upgrader extends CRM_Extension_Upgrader_Base {
  /**
   * @throws UnauthorizedException
   * @throws CRM_Core_Exception
   */
  public function install(): void {
    \Civi\Api4\OptionGroup::save(FALSE)
      ->addRecord([
        'name' => 'api_request_status',
        'title' => E::ts('API Request Type'),
      ])
      ->setMatch(['name'])
      ->execute();

    \Civi\Api4\OptionValue::save(FALSE)
      ->setDefaults([
        'option_group_id.name' => 'api_request_status',
      ])
      ->setRecords([
        ['value' => 1, 'name' => 'All Requests', 'label' => E::ts('All Requests')],
        ['value' => 2, 'name' => 'Successful Request', 'label' => E::ts('Successful Request')],
        ['value' => 3, 'name' => 'Failed Request', 'label' => E::ts('Failed Request')],
      ])
      ->setMatch(['option_group_id', 'name'])
      ->execute();
  }
}