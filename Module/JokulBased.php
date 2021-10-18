<?php

class JokulBased {

  const SANDBOX_BASE_URL    = 'https://api-sandbox.doku.com';
  const PRODUCTION_BASE_URL = 'https://api.doku.com';

  /**
   * @return string Doku API URL, depends on $state
   */
  public function getBaseUrl($state)
  {
    return ($state == 'production') ? JokulConfig::PRODUCTION_BASE_URL : JokulConfig::SANDBOX_BASE_URL;
  }
}

?>
