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
    return $filterValue == '*' || preg_match('/' . $filterValue . '/i', $field);
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
    if ($event instanceof \Civi\API\Event\ExceptionEvent) {
      $apiResponse = self::formatException($event);
    }
    else {
      $apiResponse = $event->getResponse();
    }

    if ($apiResponse && isset($apiResponse['is_error']) && $apiResponse['is_error'] !== 0) {
      $isSuccess = false;
    }

    if (self::shouldLogApiRequest($apiRequest, $apiResponse, $isSuccess, $config)) {
      CRM_ApiLog_BAO_ApiLog::create([
        'contact_id' => CRM_Core_Session::getLoggedInContactID(),
        'api_log_config_id' => $config['id'],
        'api_entity' => $apiRequest['entity'],
        'api_action' => $apiRequest['action'],
        'request' => json_encode(is_array($apiRequest) ? $apiRequest['params'] ?? [] : $apiRequest->getParams()),
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

    if ($expression == '*') {
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

  /**
   * Format an ExceptionEvent according to the implementation
   * in \Civi\API\Kernel (API3) or CRM_Api4_Page_AJAX (API4)
   *
   * @param \Civi\API\Event\ExceptionEvent $event
   *
   * @return mixed
   * @throws \CRM_Core_Exception
   */
  private static function formatException(\Civi\API\Event\ExceptionEvent $event) {
    $apiRequest = $event->getApiRequest();
    $e = $event->getException();
    if ($apiRequest['version'] == 3) {
      $kernel = $event->getApiKernel();
      if ($e instanceof \CRM_Core_Exception) {
        $err = $kernel->formatApiException($e, $apiRequest);
      }
      elseif ($e instanceof \PEAR_Exception) {
        $err = $kernel->formatPearException($e, $apiRequest);
      }
      else {
        $err = $kernel->formatException($e, $apiRequest);
      }
      return $kernel->formatResult($apiRequest, $err);
    }
    else {
      $response = [];
      $statusMap = [
        \Civi\API\Exception\UnauthorizedException::class => 403,
      ];
      $response['status'] = $statusMap[get_class($e)] ?? 500;
      if (method_exists($e, 'getErrorData')) {
        $response['error_code'] = $e->getCode();
        $response['error_message'] = $e->getMessage();
        if (method_exists($e, 'getUserInfo')) {
          $response['debug']['info'] = $e->getUserInfo();
        }
        $cause = method_exists($e, 'getCause') ? $e->getCause() : $e;
        if ($cause instanceof \DB_Error) {
          $response['debug']['db_error'] = \DB::errorMessage($cause->getCode());
          $response['debug']['sql'][] = $cause->getDebugInfo();
        }
        // Would prefer getTrace() but that causes json_encode to bomb
        $response['debug']['backtrace'] = $e->getTraceAsString();
      }
      else {
        $response['error_code'] = 1;
        $response['error_message'] = $e->getMessage();
        $response['debug']['backtrace'] = $e->getTraceAsString();
      }
      return $response;
    }
  }

}
