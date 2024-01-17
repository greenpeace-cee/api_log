<?php


namespace Civi\Utils;

use Civi;

class Settings {
  const TITLE = 'ApiLog';

  const API_LOG_IS_LOGGER_ENABLED = 'api_log_is_logger_enabled';

  public static function getIsApiLoggerEnabled(): bool {
    $isEnabled = Civi::settings()->get(Settings::API_LOG_IS_LOGGER_ENABLED);
    return !empty($isEnabled);
  }

  public static function setIsApiLoggerEnabled($filterValue): void {
    Civi::settings()->set(Settings::API_LOG_IS_LOGGER_ENABLED, $filterValue);
  }
}