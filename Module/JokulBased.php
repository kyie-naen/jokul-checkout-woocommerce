<?php

class JokulBased {
  
  /**
   * @return string Doku API URL, depends on $state
   */
  public function getBaseUrl($state)
  {
    // return $state;
    return ($state == 'production') ? 'https://api.doku.com' : 'https://api-sandbox.doku.com';
  }
}

?>
