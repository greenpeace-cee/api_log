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

    if (!empty($params['id'])) {
      CRM_Utils_Hook::pre('edit', self::getEntityName(), $params['id'], $params);
    } else {
      CRM_Utils_Hook::pre('create', self::getEntityName(), NULL, $params);
    }

    $instance->save();

    return $instance;
  }

  /**
   * Delete config
   * @param $id
   */
  public static function del($id) {
    $entity = new CRM_Apilog_BAO_ApiLogConfig();
    $entity->id = $id;
    $params = [];

    if ($entity->find(TRUE)) {
      CRM_Utils_Hook::pre('delete', self::getEntityName(), $entity->id, $params);
      $entity->delete();
      CRM_Utils_Hook::post('delete', self::getEntityName(), $entity->id, $entity);
    }
  }

  private static function buildWhereQuery($query, $params = []) {
    if (!empty($params['id'])) {
      $query->where('id = #id', ['id' => $params['id']]);
    }

    if (!empty($params['title'])) {
      $query->where('title = @title', ['title' => $params['title']]);
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

  /**
   * Gets all data
   *
   * @param array $params
   *
   * @return array
   */
  public static function getAll($params = []) {
    $query = self::buildOrderQuery(self::buildWhereQuery(self::buildSelectQuery(), $params), $params);
    return CRM_Core_DAO::executeQuery($query->toSQL())->fetchAll();
  }


  /**
   * Adds order params to query
   *
   * @param $query
   * @param array $params
   * @return mixed
   */
  private static function buildOrderQuery($query, array $params = []) {
    if (!empty($params['options']['sort'])) {
      $sortParams = explode(' ', strtolower($params['options']['sort']));
      $availableFieldsToSort = ['weight'];
      $order = '';

      if (!empty($sortParams[1]) && ($sortParams[1] == 'desc' || $sortParams[1] == 'asc')) {
        $order = $sortParams[1];
      }

      if (in_array($sortParams[0], $availableFieldsToSort)) {
        $query->orderBy($sortParams[0] . ' ' . $order);
      }
    }

    return $query;
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

}
