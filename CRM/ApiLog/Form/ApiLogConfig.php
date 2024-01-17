<?php

use Civi\Utils\Settings;
use CRM_ApiLog_ExtensionUtil as E;

class CRM_ApiLog_Form_ApiLogConfig extends CRM_Core_Form {

  protected $apiConfig;

  public function preProcess(): void {
    $this->apiConfig = civicrm_api4('ApiLogConfig', 'get', [
      'where' => [
        ['id', '=', CRM_Utils_Request::retrieve('id', 'Positive')],
      ]
    ])->first();

    $this->assign('apiConfig', $this->apiConfig);
  }

  public function buildQuickForm(): void {
    parent::buildQuickForm();

    $buttons = [];

    if ($this->getAction() == CRM_Core_Action::ADD || $this->getAction() == CRM_Core_Action::UPDATE) {
      CRM_Utils_System::setTitle(Settings::TITLE . ' - ' . E::ts('Settings'));

      $apiRequestsStatuses = CRM_Core_OptionGroup::values('api_request_status');

      $this->add('text', 'title', E::ts('Title'), ['class' => 'huge', 'placeholder' => 'Enter Title']);
      $this->add('text', 'entity_filter', E::ts('Filter by Entity'), ['class' => 'huge', 'placeholder' => 'Enter Entity']);
      $this->add('text', 'action_filter', E::ts('Filter by Action'), ['class' => 'huge', 'placeholder' => 'Enter Action']);
      $this->add('textarea', 'request_filter', E::ts('Filter by Request'), ['class' => 'huge', 'placeholder' => 'Enter Value']);
      $this->add('textarea', 'response_filter', E::ts('Filter by Response'), ['class' => 'huge', 'placeholder' => 'Enter Value']);
      $this->add('select', 'success_filter', E::ts('Filter by Success'), $apiRequestsStatuses, false, ['class' => 'huge crm-select2']);

      $this->assign('settingsNames', ['title', 'entity_filter', 'action_filter', 'request_filter', 'response_filter', 'success_filter']);

      $buttons[] = ['type' => 'submit', 'name' => E::ts('Save'), 'isDefault' => TRUE];
    }

    if ($this->getAction() == CRM_Core_Action::UPDATE) {
      $this->add('hidden', 'id', $this->apiConfig['id']);
    }

    if ($this->getAction() == CRM_Core_Action::DELETE) {
      $this->add('hidden', 'id', $this->apiConfig['id']);
      $buttons[] = ['type' => 'submit', 'name' => E::ts('Delete'), 'isDefault' => TRUE];
    }

    $buttons[] = ['type' => 'cancel', 'name' => E::ts('Cancel'), 'class' => 'cancel'];
    $this->addButtons($buttons);
  }

  public function setDefaultValues(): array {
    $defaults = [];

    if ($this->getAction() == CRM_Core_Action::UPDATE) {
      $defaults['title'] = $this->apiConfig['title'];
      $defaults['entity_filter'] = $this->apiConfig['entity_filter'];
      $defaults['action_filter'] = $this->apiConfig['action_filter'];
      $defaults['request_filter'] = $this->apiConfig['request_filter'];
      $defaults['response_filter'] = $this->apiConfig['response_filter'];
      $defaults['success_filter'] = $this->apiConfig['success_filter'];
    }

    return $defaults;
  }


  public function postProcess(): void {
    $values = $this->exportValues();

    if ($this->getAction() == CRM_Core_Action::DELETE) {
      civicrm_api4('ApiLogConfig', 'delete', [
        'where' => [['id', '=', $values['id']]]
      ]);

      CRM_Core_Session::setStatus(E::ts('The Configuration was successfully deleted!'), E::ts('Success'), 'success');
      CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/api-log"));
    }

    $apiParams = [
      'title' => $values['title'],
      'entity_filter' => $values['entity_filter'],
      'action_filter' => $values['action_filter'],
      'request_filter' => $values['request_filter'],
      'response_filter' => $values['response_filter'],
      'success_filter' => $values['success_filter'],
    ];


    if ($this->getAction() == CRM_Core_Action::ADD) {
      civicrm_api4('ApiLogConfig', 'create', [
        'values' => $apiParams
      ]);

      CRM_Core_Session::setStatus(E::ts('The Configuration was successfully created!'), E::ts('Success'), 'success');
    }

    if ($this->getAction() == CRM_Core_Action::UPDATE) {
      civicrm_api4('ApiLogConfig', 'update', [
        'values' => $apiParams,
        'where' => [['id', '=', $values['id']]]
      ]);

      CRM_Core_Session::setStatus(E::ts('The Configuration was successfully updated!'), E::ts('Success'), 'success');
    }

    CRM_Utils_System::redirect(CRM_Utils_System::url("civicrm/api-log"));
    CRM_Core_Session::setStatus(E::ts("Your Configurations has been saved."), E::ts('Saved'), 'success');
    parent::postProcess();
  }

  public function addRules(): void {
    if ($this->getAction() == CRM_Core_Action::ADD || $this->getAction() == CRM_Core_Action::UPDATE) {
      $this->addFormRule([self::class, 'validateForm']);
    }
  }

  public static function validateForm($values) {
    $errors = [];

    if (strlen($values['title']) > 255) {
      $errors['title'] = E::ts('Title length must be less than 255 characters.');
    }

    if (empty($values['title'])) {
      $errors['title'] = E::ts('Title should be not empty.');
    }

    if (!CRM_ApiLog_Utils_ApiLogConfig::isUniqueTitle($values['id'] ?? null, $values['title'])) {
      $errors['title'] = 'Title "' . $values['title'] . '" already exists.';
    }

    return empty($errors) ? TRUE : $errors;
  }
}
