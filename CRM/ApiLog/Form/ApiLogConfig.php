<?php

use Civi\Utils\Settings;
use CRM_ApiLog_ExtensionUtil as E;

class CRM_ApiLog_Form_ApiLogConfig extends CRM_Core_Form {

  public function buildQuickForm(): void {
    CRM_Utils_System::setTitle(Settings::TITLE . ' - ' . E::ts('Settings'));

    #TODO: Add field with option group
    $this->add('text', Settings::API_LOG_ENTITY_FILTER, E::ts('Define regular expressions for entity'), ['class' => 'huge', 'placeholder' => 'Enter Entity_%_']);
    $this->add('text', Settings::API_LOG_ACTION_FILTER, E::ts('Define regular expressions for action'), ['class' => 'huge', 'placeholder' => 'Enter Action_%_']);
    $this->add('textarea', Settings::API_LOG_REQUEST_FILTER, E::ts('Define regular expressions for request'), ['class' => 'huge', 'placeholder' => 'Enter Value_%_']);
    $this->add('textarea', Settings::API_LOG_RESPONSE_FILTER, E::ts('Define regular expressions for response'), ['class' => 'huge', 'placeholder' => 'Enter Value_%_']);
//    $this->addEntityRef('text', Settings::API_LOG_RESPONSE_FILTER, E::ts('Define regular expressions for response'), ['class' => 'huge', 'placeholder' => 'Enter Value_%_']);

    $this->assign('settingsNames', [Settings::API_LOG_ENTITY_FILTER, Settings::API_LOG_ACTION_FILTER, Settings::API_LOG_REQUEST_FILTER, Settings::API_LOG_RESPONSE_FILTER]);

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
      Settings::API_LOG_ENTITY_FILTER => Settings::getEntityFilterValues(),
      Settings::API_LOG_ACTION_FILTER => Settings::getActionFilterValues(),
      Settings::API_LOG_REQUEST_FILTER => Settings::getRequestFilterValues(),
      Settings::API_LOG_RESPONSE_FILTER => Settings::getResponseFilterValues(),
    ];
  }


  public function postProcess(): void {
    $values = $this->exportValues();

    Settings::setEntityFilterValues($values[Settings::API_LOG_ENTITY_FILTER] ?? '');

    Settings::setActionFilterValues($values[Settings::API_LOG_ACTION_FILTER] ?? '');

    Settings::setRequestFilterValues($values[Settings::API_LOG_REQUEST_FILTER] ?? '');

    Settings::setResponseFilterValues($values[Settings::API_LOG_RESPONSE_FILTER] ?? '');

    CRM_Core_Session::setStatus(E::ts('Saved!'));
    parent::postProcess();
  }

}
