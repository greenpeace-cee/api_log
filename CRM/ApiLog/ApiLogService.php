<?php

use Civi\API\Events;
use Civi\Utils\Settings;
use Civi\Core\Service\AutoService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

$civicrm_base_path = dirname(__DIR__, 2);
require $civicrm_base_path . '/vendor/autoload.php';

class CRM_ApiLog_ApiLogService extends AutoService implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'civi.api.respond' => ['onApiRespond', Events::W_LATE],
      'civi.api.prepare' => ['onApiPrepare', Events::W_EARLY],
      'civi.api.exception' => ['onApiException', Events::W_LATE],
    ];
  }

  public static function onApiRespond($event): void {
    self::handleApiEvent($event, true);
  }

  public static function onApiException($event): void {
    self::handleApiEvent($event, false);
  }

  public function onApiPrepare($event): void {
    self::handleApiEvent($event, true);
  }

  private static function handleApiEvent($event, $isSuccess): void {
    $apiRequest = $event->getApiRequest();
    $apiResponse = method_exists($event, 'getResponse') ? $event->getResponse() : null;

    $entityValues = self::parseFilterValues(Settings::getEntityFilterValues());
    $actionValues = self::parseFilterValues(Settings::getActionFilterValues());

    if (
      self::isEntityOrActionAllowed($apiRequest, $entityValues, $actionValues)
      || self::isAllowedBasedOnJmesPath($apiRequest, Settings::getRequestFilterValues())
      || self::isAllowedBasedOnJmesPath($apiResponse, Settings::getResponseFilterValues())
    ) {
      self::logApiRequest($apiRequest, $apiResponse, $isSuccess);
    }
  }

  private static function logApiRequest($apiRequest, $apiResponse, $isSuccess): void {
    CRM_ApiLog_BAO_ApiLog::create([
      'contact_id' => CRM_Core_Session::getLoggedInContactID(),
      'api_entity' => $apiRequest['entity'] ?? null,
      'api_action' => $apiRequest['action'] ?? null,
      'request' => json_encode($apiRequest),
      'response' => json_encode($apiResponse),
      'api_version' => $apiRequest['version'] ?? null,
      'success' => $isSuccess,
      'created_date' => date('YmdHis'),
    ]);
  }

  private static function isEntityOrActionAllowed($apiRequest, $entityValues, $actionValues): bool {
    return (isset($apiRequest['entity']) && self::isAllowedBasedOnRegexValue($apiRequest['entity'], $entityValues))
      || (isset($apiRequest['action']) && self::isAllowedBasedOnRegexValue($apiRequest['action'], $actionValues));
  }

  private static function isAllowedBasedOnRegexValue($value, $filterValues): bool {
    foreach ($filterValues as $filterValue) {
      $pattern = '/' . preg_quote($filterValue, '/') . '/i';

      if (preg_match($pattern, $value)) {
        return true;
      }
    }
    return false;
  }

  private static function parseFilterValues($filterValues): array {
    return is_array($filterValues) ? $filterValues : explode('_&_', $filterValues);
  }

  private static function isAllowedBasedOnJmesPath($data, $expression): bool {
    if (empty($expression)) {
      return false;
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
