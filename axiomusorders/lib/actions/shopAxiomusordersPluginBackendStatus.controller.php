<?php

class shopAxiomusordersPluginBackendStatusController extends waJsonController
{

  public function execute(){

      $app_settings_model = new waAppSettingsModel();
      $key = array('shop', 'axiomusorders');
      $ukey = $app_settings_model->get($key, 'ukey');

      $orderID = waRequest::request('orderID');
      $order_model = new shopOrderModel();
      $order = $order_model->getById($orderID);

      if($order['axiomus_orderID']==''){

        $this->response = array(
                                  'status' => 'error',
                                  'axiomusResultID' => $order['axiomus_orderID'],
                          );
        return;
      }

      $xml = '<?xml version="1.0" standalone="yes"?>
                <singleorder>
                <mode>status</mode>
                <auth ukey="'.$ukey.'" />
                <okey>'.$order['axiomus_okey'].'</okey>
              </singleorder>';

      $axiPlugin = new shopAxiomusordersPlugin(true);
      $curlRes = $axiPlugin->sendXml($xml);

      $result = false;
      if($curlRes['error']=='' && $curlRes['result']!=''){

         $result = simplexml_load_string( $curlRes['result'] );
      }

      if($curlRes['error']!=''){

        $this->response = array(
                                'status' => 'error',
                                'error' => $curlRes['error'],
                          );
        return false;

      } elseif(!$result){

        $xmlError = "Failed loading XML<br />";
        foreach(libxml_get_errors() as $error) {
          $xmlError .= "<br />".$error->message;
        }

        $this->response = array(
                                'status' => 'error',
                                'error' => $xmlError,
                          );
        return false;

      } elseif((isset($result->status) && !isset($result->order))){

        $this->response = array(
                                'status' => 'error',
                                'error' => (string)$result->status,
                          );
        return false;

      }

      $this->response = array(
                              'status' => 'ok',
                              'statusString' => (string)$result->status,
                        );
      return;
  }

}
