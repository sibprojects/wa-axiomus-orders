<?php
class shopAxiomusordersPlugin extends shopPlugin{

   public function backend_order($order){

      $app_settings_model = new waAppSettingsModel();
      $key = array('shop', 'axiomusorders');
      $uid = $app_settings_model->get($key, 'uid');
      $ukey = $app_settings_model->get($key, 'ukey');
      $shipping_type_courier = $app_settings_model->get($key, 'shipping_type_courier');
      $shipping_type_self = $app_settings_model->get($key, 'shipping_type_self');
      $shipping_type_post = $app_settings_model->get($key, 'shipping_type_post');
      $shipping_type_dpd = $app_settings_model->get($key, 'shipping_type_dpd');
      $shipping_type_dpd_courier = $app_settings_model->get($key, 'shipping_type_dpd_courier');

      if($uid=='' || $ukey=='' || (!$shipping_type_courier && !$shipping_type_self) || (isset($order['axiomus_form_hidden']) && $order['axiomus_form_hidden']==1)){
         return array();
      }

      $view = wa()->getView();
      $view->assign('shipping_type_courier', $shipping_type_courier);
      $view->assign('shipping_type_self', $shipping_type_self);
      $view->assign('shipping_type_post', $shipping_type_post);
      $view->assign('shipping_type_dpd', $shipping_type_dpd);
      $view->assign('shipping_type_dpd_courier', $shipping_type_dpd_courier);
      $view->assign('msk_pvz_default', $app_settings_model->get($key, 'msk_pvz_default'));
      $view->assign('piter_pvz_default', $app_settings_model->get($key, 'piter_pvz_default'));

      $view->assign('sms', $app_settings_model->get($key, 'sms'));
      $view->assign('cash', $app_settings_model->get($key, 'cash'));
      $view->assign('card', $app_settings_model->get($key, 'card'));
      $view->assign('valuation', $app_settings_model->get($key, 'valuation'));
      $view->assign('post_tarif', $app_settings_model->get($key, 'post_tarif'));
      $view->assign('fragile', $app_settings_model->get($key, 'fragile'));
      $view->assign('optimize', $app_settings_model->get($key, 'optimize'));
      $view->assign('class1', $app_settings_model->get($key, 'class1'));
      $view->assign('big', $app_settings_model->get($key, 'big'));
      $view->assign('insurance', $app_settings_model->get($key, 'insurance'));

      $view->assign('now', date('Y-m-d',time()+86400));
      $view->assign('order', $order);

      $view->assign('pvz_select', $app_settings_model->get($key, 'pvz_select'));
      $view->assign('show_form_on_button', $app_settings_model->get($key, 'show_form_on_button'));

      if($order['axiomus_orderID']=='' && $shipping_type_self){

        $xml = '<?xml version="1.0" standalone="yes"?>
                  <singleorder>
                     <mode>get_boxberry_pickup</mode>
                     <auth ukey="'.$ukey.'" />
                  </singleorder>';

        $curlRes = shopAxiomusordersPlugin::sendXml($xml);
        $result = false;
        if($curlRes['error']=='' && $curlRes['result']!=''){

           $result = simplexml_load_string( $curlRes['result'] );
        }

        if($curlRes['error']!=''){

          $view->assign('xmlError', $curlRes['error']);

        } elseif(!$result){

          $xmlError = "Failed loading XML<br />";
          foreach(libxml_get_errors() as $error) {
            $xmlError .= "<br />".$error->message;
          }
          $view->assign('xmlError', $xmlError);

        } elseif(isset($result->status)){

          $view->assign('xmlError', $result->status);

        } else {

          $offices = array();
          foreach($result->pickup_list->office as $office){

            $offices[] = array( 
                                'office_code' => (string)$office->attributes()->office_code,
                                'office_name' => (string)$office->attributes()->office_name,
                                'office_address' => (string)$office->attributes()->office_address,
                                'WorkSchedule' => (string)$office->attributes()->WorkSchedule,
                                'city_name' => (string)$office->attributes()->city_name,
                              );
          }
          $view->assign('offices', $offices);
        }
      }

      if($order['axiomus_orderID']=='' && $shipping_type_dpd){

        $xml = '<?xml version="1.0" standalone="yes"?>
                  <singleorder>
                     <mode>get_dpd_pickup</mode>
                     <auth ukey="'.$ukey.'" />
                  </singleorder>';

        $curlRes = shopAxiomusordersPlugin::sendXml($xml);
        $result = false;
        if($curlRes['error']=='' && $curlRes['result']!=''){

           $result = simplexml_load_string( $curlRes['result'] );
        }

        if($curlRes['error']!=''){

          $view->assign('xmlError', $curlRes['error']);

        } elseif(!$result){

          $xmlError = "Failed loading XML<br />";
          foreach(libxml_get_errors() as $error) {
            $xmlError .= "<br />".$error->message;
          }
          $view->assign('xmlError', $xmlError);

        } elseif(isset($result->status)){

          $view->assign('xmlError', $result->status);

        } else {

          $offices = array();
          foreach($result->pickup_list->office as $office){

            $offices[] = array( 
                                'office_code' => (string)$office->attributes()->code,
                                'office_name' => (string)$office->attributes()->name,
                                'office_address' => (string)$office->attributes()->address,
                                'WorkSchedule' => (string)$office->attributes()->WorkSchedule,
                                'city_name' => (string)$office->attributes()->city,
                                'region_name' => (string)$office->attributes()->region,
                              );
          }
          $view->assign('offices_dpd', $offices);
        }
      }

      $content = $view->fetch($this->path.'/templates/order_view_admin.html');

      return array('title_suffix'=>$content);
   }

   public function sendXml($xml){

      $app_settings_model = new waAppSettingsModel();
      $key = array('shop', 'axiomusorders');
      $answer_sec = $app_settings_model->get($key, 'answer_sec');
      $answer_sec = (int)$answer_sec * 1000;

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://axiomus.ru/hydra/api_xml.php");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, "data=".urlencode($xml));
      curl_setopt($ch, CURLOPT_TIMEOUT_MS, $answer_sec);
      $result = curl_exec($ch);

      $curl_error = '';
      if ($result === FALSE) {
        $curl_error = "cURL Error: " . curl_error($ch);
      }

      curl_close($ch);
      return array('result' => $result, 'error' => $curl_error);
   }

    public static function zones()
    {
      $model = new waModel();
      $_zones = $model->query('SELECT * FROM `wa_region` WHERE `country_iso3`="rus" ORDER BY `name`')->fetchAll();
      $zones = array('' => array('value'=>'','title'=>''));
      foreach($_zones as $zone) {
         $zones[$zone['code']] = array(
                    'value' => $zone['code'],
                    'title' => $zone['name'],
                );
      }
      return $zones;
    }
}
