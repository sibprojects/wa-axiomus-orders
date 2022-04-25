<?php

class shopAxiomusordersPluginBackendHideController extends waJsonController
{

  public function execute(){

      $orderID = waRequest::request('orderID');

      $order_model = new shopOrderModel();
      $order_model->updateById($orderID, [
        'axiomus_form_hidden' => 1
      ]);

      return;
  }

}
