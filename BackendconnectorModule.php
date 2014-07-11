<?php

class BackendconnectorModule extends CWebModule {
  
  public function init() {
    $this->setImport(array(
        'backendconnector.*',
        'backendconnector.components.*',
        'backendconnector.commands.*'
    ));
  }
}
?>