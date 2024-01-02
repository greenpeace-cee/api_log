<?php


namespace Civi\Utils;

use Civi;

class Settings {
  const TITLE = 'ApiLog';
  const SEPARATOR = '_&_';

  const API_LOG_IS_LOGGER_ENABLED = 'api_log_is_logger_enabled';
  const API_LOG_ENTITY_FILTER = 'api_log_entity_filter';
  const API_LOG_ACTION_FILTER = 'api_log_action_filter';
  const API_LOG_REQUEST_FILTER = 'api_log_request_filter';
  const API_LOG_RESPONSE_FILTER = 'api_log_response_filter';
  const API_LOG_SUCCESS_FILTER = 'api_request_status';

  public static function getEntityFilterValues() {
    return Civi::settings()->get(Settings::API_LOG_ENTITY_FILTER);
  }

  public static function getActionFilterValues() {
    return Civi::settings()->get(Settings::API_LOG_ACTION_FILTER);
  }

  public static function getRequestFilterValues() {
    return Civi::settings()->get(Settings::API_LOG_REQUEST_FILTER);
  }

  public static function getResponseFilterValues() {
    return Civi::settings()->get(Settings::API_LOG_RESPONSE_FILTER);
  }

  public static function getSuccessFilterValues() {
    return Civi::settings()->get(Settings::API_LOG_SUCCESS_FILTER);
  }

  public static function getIsApiLoggerEnabled() {
    return Civi::settings()->get(Settings::API_LOG_IS_LOGGER_ENABLED);
  }

  public static function setEntityFilterValues($filterValue): void {
    Civi::settings()->set(Settings::API_LOG_ENTITY_FILTER, $filterValue);
  }

  public static function setActionFilterValues($filterValue): void {
    Civi::settings()->set(Settings::API_LOG_ACTION_FILTER, $filterValue);
  }

  public static function setRequestFilterValues($filterValue): void {
    Civi::settings()->set(Settings::API_LOG_REQUEST_FILTER, $filterValue);
  }

  public static function setResponseFilterValues($filterValue): void {
    Civi::settings()->set(Settings::API_LOG_RESPONSE_FILTER, $filterValue);
  }

  public static function setSuccessFilterValues($filterValue): void {
    Civi::settings()->set(Settings::API_LOG_SUCCESS_FILTER, $filterValue);
  }

  public static function setIsApiLoggerEnabled($filterValue): void {
    Civi::settings()->set(Settings::API_LOG_IS_LOGGER_ENABLED, $filterValue);
  }
}