<?php

use Civi\API\Events;
use Civi\Utils\Settings;
use Civi\Core\Service\AutoService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

$civicrmBasePath = dirname(__DIR__, 2);
require $civicrmBasePath . '/vendor/autoload.php';

class CRM_ApiLog_ApiLogService extends AutoService implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'civi.api.respond' => ['onApiRespond', Events::W_LATE],
      'civi.api.exception' => ['onApiException', Events::W_LATE],
    ];
  }

  public static function getConfigs(): array {
    return CRM_ApiLog_BAO_ApiLogConfig::getAll();
  }

  public static function onApiRespond($event): void {
    self::handleApiEvent($event, true);
  }

  public static function onApiException($event): void {
    self::handleApiEvent($event, false);
  }

  private static function isPassConfig(array $config, $event): bool {
    $apiRequest = $event->getApiRequest();

    if (!self::shouldCheckField($config['entity_filter'], $apiRequest['entity']) ||
      !self::shouldCheckField($config['action_filter'], $apiRequest['action'])) {
      return false;
    }

    return true;
  }

  private static function shouldCheckField($filterValue, $field): bool {
    return str_contains($filterValue, '*') || preg_match('/' . preg_quote($filterValue, '/') . '/i', $field);
  }

  public static function handleApiEvent($event, $isSuccess): void {
    foreach (self::getConfigs() as $config) {

      if (self::isPassConfig($config, $event)) {
        self::logApiRequest($event, $isSuccess, $config);
      }
    }
  }

  private static function logApiRequest($event, $isSuccess, $config): void {
    $apiRequest = $event->getApiRequest();
    $apiResponse = method_exists($event, 'getResponse') ? $event->getResponse() : null;

    if ($apiResponse && isset($apiResponse['is_error']) && $apiResponse['is_error'] !== 0) {
      $isSuccess = false;
    }

    if (self::shouldLogApiRequest($apiRequest, $apiResponse, $isSuccess, $config)) {
      CRM_ApiLog_BAO_ApiLog::create([
        'contact_id' => CRM_Core_Session::getLoggedInContactID(),
        'api_log_config_id' => $config['id'],
        'api_entity' => $apiRequest['entity'],
        'api_action' => $apiRequest['action'],
        'request' => json_encode($apiRequest),
        'response' => json_encode($apiResponse),
        'api_version' => $apiRequest['version'] ?? null,
        'success' => $isSuccess,
        'created_date' => date('YmdHis'),
      ]);
    }
  }

  private static function shouldLogApiRequest($apiRequest, $apiResponse, $isSuccess, $config): bool {
    $logBySuccessOption = $config['success_filter'];

    return (
      self::isAllowedBasedOnJmesPath($apiRequest, $config['request_filter'])
      && self::isAllowedBasedOnJmesPath($apiResponse, $config['response_filter'])
      &&
      (
        $logBySuccessOption == 1
        || ($logBySuccessOption == 2 && $isSuccess)
        || ($logBySuccessOption == 3 && !$isSuccess)
      )
    );
  }

  private static function isAllowedBasedOnJmesPath($data, $expression): bool {
    if (empty($expression)) {
      return false;
    }

    if (str_contains($expression, '*')) {
      return true;
    }

    $filterValues = is_array($expression) ? $expression : explode('_&_', $expression);

    foreach ($filterValues as $filterValue) {
      if (!empty(JMESPath\search(trim($filterValue), $data))) {
        return true;
      }
    }

    return false;
  }
}
