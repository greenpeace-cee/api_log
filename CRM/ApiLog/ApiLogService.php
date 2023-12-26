<?php

use Civi\API\Events;
use CRM_ApiLog_ExtensionUtil as E;
use Civi\Core\Service\AutoService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @service apiLogService
 */
class CRM_ApiLog_ApiLogService extends AutoService implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'civi.api.respond' => ['onApiRespond', Events::W_LATE],
      'civi.api.prepare' => ['onApiPrepare', Events::W_LATE],
      'civi.api.exception' => ['onApiException', Events::W_LATE],
    ];
  }

  public static function onApiRespond($event): void {
    $apiRequest = $event->getApiRequest();

    $apiResponse = method_exists($event, 'getResponse') ? $event->getResponse() : null;

    self::logApiRequest($apiRequest, $apiResponse, true);
  }

  #TODO: find a solution for api4 exceptions
  public static function onApiException($event): void {
    $apiRequest = $event->getApiRequest();

    $apiResponse = method_exists($event, 'getResponse') ? $event->getResponse() : null;

    self::logApiRequest($apiRequest, $apiResponse, false);
  }

  public function onApiPrepare($event): void {
    $apiRequest = $event->getApiRequest();

    $apiResponse = method_exists($event, 'getResponse') ? $event->getResponse() : null;

    self::logApiRequest($apiRequest, $apiResponse, true);
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
      'created_date' => date('YmdHis')
    ]);
  }
}
