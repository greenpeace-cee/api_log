<?php

use Civi\Utils\Settings;
use CRM_ApiLog_ExtensionUtil as E;

class CRM_ApiLog_Form_ApiLogConfig extends CRM_Core_Form {

  public function buildQuickForm(): void {
    CRM_Utils_System::setTitle(Settings::TITLE . ' - ' . E::ts('Settings'));

    $apiRequestsStatuses = CRM_Core_OptionGroup::values('api_request_status');
    $this->addElement('checkbox', Settings::API_LOG_IS_LOGGER_ENABLED, E::ts('Is API logger enabled?'));
    $this->add('text', Settings::API_LOG_ENTITY_FILTER, E::ts('Filter by Entity'), ['class' => 'huge', 'placeholder' => 'Enter Entity_%_']);
    $this->add('text', Settings::API_LOG_ACTION_FILTER, E::ts('Filter by Action'), ['class' => 'huge', 'placeholder' => 'Enter Action_%_']);
    $this->add('textarea', Settings::API_LOG_REQUEST_FILTER, E::ts('Filter by Request'), ['class' => 'huge', 'placeholder' => 'Enter Value_%_']);
    $this->add('textarea', Settings::API_LOG_RESPONSE_FILTER, E::ts('Filter by Response'), ['class' => 'huge', 'placeholder' => 'Enter Value_%_']);
    $this->add('select', Settings::API_LOG_SUCCESS_FILTER, E::ts('Filter by Success'), $apiRequestsStatuses, false, ['class' => 'huge crm-select2']);

    $this->assign('settingsNames', [Settings::API_LOG_IS_LOGGER_ENABLED, Settings::API_LOG_ENTITY_FILTER, Settings::API_LOG_ACTION_FILTER, Settings::API_LOG_REQUEST_FILTER, Settings::API_LOG_RESPONSE_FILTER, Settings::API_LOG_SUCCESS_FILTER]);

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ],
    ]);

    parent::buildQuickForm();
  }

  public function setDefaultValues(): array {
    return [
      Settings::API_LOG_IS_LOGGER_ENABLED => Settings::getIsApiLoggerEnabled(),
      Settings::API_LOG_ENTITY_FILTER => Settings::getEntityFilterValues(),
      Settings::API_LOG_ACTION_FILTER => Settings::getActionFilterValues(),
      Settings::API_LOG_REQUEST_FILTER => Settings::getRequestFilterValues(),
      Settings::API_LOG_RESPONSE_FILTER => Settings::getResponseFilterValues(),
      Settings::API_LOG_SUCCESS_FILTER => Settings::getSuccessFilterValues(),
    ];
  }


  public function postProcess(): void {
    $values = $this->exportValues();

    Settings::setIsApiLoggerEnabled($values[Settings::API_LOG_IS_LOGGER_ENABLED]);
    Settings::setEntityFilterValues($values[Settings::API_LOG_ENTITY_FILTER] ?? '');
    Settings::setActionFilterValues($values[Settings::API_LOG_ACTION_FILTER] ?? '');
    Settings::setRequestFilterValues($values[Settings::API_LOG_REQUEST_FILTER] ?? '');
    Settings::setResponseFilterValues($values[Settings::API_LOG_RESPONSE_FILTER] ?? '');
    Settings::setSuccessFilterValues($values[Settings::API_LOG_SUCCESS_FILTER]);

    CRM_Core_Session::setStatus(E::ts("Your Configurations has been saved."), E::ts('Saved'), 'success');
    parent::postProcess();
  }

}
