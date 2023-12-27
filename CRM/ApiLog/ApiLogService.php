<?php

use Civi\API\Events;
use Civi\Utils\Settings;
use CRM_ApiLog_ExtensionUtil as E;
use Civi\Core\Service\AutoService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

$civicrm_base_path = dirname(__DIR__, 2);
require $civicrm_base_path . '/vendor/autoload.php';

class CRM_ApiLog_ApiLogService extends AutoService implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'civi.api.respond' => ['onApiRespond', Events::W_LATE],
      // 'civi.api.prepare' => ['onApiPrepare', Events::W_LATE],
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

    $entityValuesArray = self::parseFilterValues(Settings::getEntityFilterValues());
    $actionValuesArray = self::parseFilterValues(Settings::getActionFilterValues());

    if (self::isEntityOrActionAllowed($apiRequest, $entityValuesArray, $actionValuesArray)
      || self::isRecordAllowed($apiRequest, Settings::getRequestFilterValues())
      || self::isRecordAllowed($apiResponse, Settings::getResponseFilterValues())) {
      self::logApiRequest($apiRequest, $apiResponse, $isSuccess);
    }
  }

  private static function isEntityOrActionAllowed($apiRequest, $entityValues, $actionValues): bool {
    return (isset($apiRequest['entity']) && self::isAllowed($apiRequest['entity'], $entityValues))
      || (isset($apiRequest['action']) && self::isAllowed($apiRequest['action'], $actionValues));
  }

  private static function isAllowed($value, $filterValues): bool {
    foreach ($filterValues as $filterValue) {
      if (str_contains($value, $filterValue)) {
        return true;
      }
    }
    return false;
  }

  private static function logApiRequest($apiRequest, $apiResponse, $isSuccess): void {
    CRM_ApiLog_BAO_ApiLog::create([
      'contact_id' => CRM_Core_Session::getLoggedInContactID(),
      'api_entity' => $apiRequest['entity'],
      'api_action' => $apiRequest['action'],
      'request' => json_encode($apiRequest),
      'response' => json_encode($apiResponse),
      'api_version' => $apiRequest['version'],
      'success' => $isSuccess,
      'created_date' => date('YmdHis'),
    ]);
  }

  private static function parseFilterValues($filterValues): array {
    return $filterValues ? (str_contains($filterValues, '_&_') ? explode('_&_', $filterValues) : [$filterValues]) : [];
  }

  private static function isRecordAllowed($data, $expression): bool {
    if (empty($expression)) {
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
