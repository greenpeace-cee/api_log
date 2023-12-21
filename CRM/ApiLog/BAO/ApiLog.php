<?php

use CRM_ApiLog_ExtensionUtil as E;

class CRM_ApiLog_BAO_ApiLog extends CRM_ApiLog_DAO_ApiLog {

  private static $_entityName = 'ApiLog';

  public static function getEntityName() {
    return self::$_entityName;
  }

  public static function create($params) {
    CRM_Utils_Hook::pre('create', self::getEntityName(), CRM_Utils_Array::value('id', $params), $params);

    $instance = new self();
    $instance->copyValues($params);

    $instance->save();

    CRM_Utils_Hook::post('create', self::getEntityName(), $instance->id, $instance);

    return $instance;
  }

  private static function buildSelectQuery($returnValue = 'rows') {
    $query = CRM_Utils_SQL_Select::from(self::getTableName());

    if ($returnValue == 'rows') {
      $query->select('*');
    } else {
      if ($returnValue == 'count') {
        $query->select('COUNT(id)');
      }
    }

    return $query;
  }

  private static function buildWhereQuery($query, $params = []) {
    if (!empty($params['id'])) {
      $query->where('id = #id', ['id' => $params['id']]);
    }

    if (!empty($params['api_entity'])) {
      $query->where('api_entity = @api_entity', ['api_entity' => $params['api_entity']]);
    }

    if (!empty($params['api_action'])) {
      $query->where('api_action = @api_action', ['api_action' => $params['api_action']]);
    }

    if (!empty($params['request'])) {
      $query->where('request = @request', ['request' => $params['request']]);
    }

    if (!empty($params['response'])) {
      $query->where('response = @response', ['response' => $params['response']]);
    }

    if (!empty($params['success'])) {
      $query->where('success = @success', ['success' => $params['success']]);
    }

    if (!empty($params['api_version'])) {
      $query->where('api_version = @api_version', ['api_version' => $params['api_version']]);
    }

    if (!empty($params['contact_id'])) {
      $query->where('contact_id = @contact_id', ['contact_id' => $params['contact_id']]);
    }

    if (!empty($params['created_date'])) {
      $query->where('created_date = @created_date', ['created_date' => $params['created_date']]);
    }

    return $query;
  }


}
