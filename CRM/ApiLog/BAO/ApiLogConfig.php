<?php

use Civi\Core\Exception\DBQueryException;
use CRM_ApiLog_ExtensionUtil as E;

class CRM_ApiLog_BAO_ApiLogConfig extends CRM_ApiLog_DAO_ApiLogConfig {

  private static $_entityName = 'ApiLogConfig';

  public static function getEntityName(): string {
    return self::$_entityName;
  }

  public static function create($params): CRM_Apilog_BAO_ApiLogConfig {
    CRM_Utils_Hook::pre('create', self::getEntityName(), CRM_Utils_Array::value('id', $params), $params);

    $instance = new self();
    $instance->copyValues($params);

    $instance->save();

    CRM_Utils_Hook::post('create', self::getEntityName(), $instance->id, $instance);

    return $instance;
  }

  private static function buildWhereQuery($query, $params = []) {
    if (!empty($params['id'])) {
      $query->where('id = #id', ['id' => $params['id']]);
    }

    if (!empty($params['entity_filter'])) {
      $query->where('entity_filter = @entity_filter', ['entity_filter' => $params['entity_filter']]);
    }

    if (!empty($params['action_filter'])) {
      $query->where('action_filter = @action_filter', ['action_filter' => $params['action_filter']]);
    }

    if (!empty($params['request_filter'])) {
      $query->where('request_filter = @request_filter', ['request_filter' => $params['request_filter']]);
    }

    if (!empty($params['response_filter'])) {
      $query->where('response_filter = @response_filter', ['response_filter' => $params['response_filter']]);
    }

    if (!empty($params['success_filter'])) {
      $query->where('success_filter = @success_filter', ['success_filter' => $params['success_filter']]);
    }

    return $query;
  }

}
