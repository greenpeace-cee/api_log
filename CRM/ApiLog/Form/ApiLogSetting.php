<?php

use Civi\Utils\Settings;
use CRM_ApiLog_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_ApiLog_Form_ApiLogSetting extends CRM_Core_Form {

  public function buildQuickForm() {
    $this->setTitle(E::ts('API log Settings'));
    $this->add('checkbox', Settings::API_LOG_IS_LOGGER_ENABLED, E::ts('API log enabled?'));

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ]
    ]);

    $this->assign('loggerValue', Settings::API_LOG_IS_LOGGER_ENABLED);


    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    return [
      Settings::API_LOG_IS_LOGGER_ENABLED => Settings::getIsApiLoggerEnabled(),
    ];
  }

  public function postProcess() {
    $values = $this->exportValues();

    if (!empty($values[Settings::API_LOG_IS_LOGGER_ENABLED])) {
      Settings::setIsApiLoggerEnabled($values[Settings::API_LOG_IS_LOGGER_ENABLED]);
    }

    CRM_Core_Session::setStatus(E::ts('Saved!'));
    parent::postProcess();
  }

}
