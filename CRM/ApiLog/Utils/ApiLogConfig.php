<?php

class CRM_ApiLog_Utils_ApiLogConfig {

  public static function isUniqueTitle($id, $title): bool {
    return civicrm_api4('ApiLogConfig', 'get', [
        'where' => [
          ['id', '!=', $id],
          ['title', '=', $title]
        ]
      ])->count() === 0;
  }
}
