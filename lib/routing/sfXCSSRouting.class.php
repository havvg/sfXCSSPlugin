<?php

class sfXCSSRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
    $r->prependRoute('sf_xcss_process', new sfRoute('/xcss/:file', array('module' => 'xcss', 'action' => 'process'), array('file' => '\w.+')));
  }
}