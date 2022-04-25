<?php

class shopAxiomusordersPluginBackendSendController extends waJsonController
{

  public function execute(){

      $app_settings_model = new waAppSettingsModel();
      $key = ['shop', 'axiomusorders'];

      $uid = $app_settings_model->get($key, 'uid');
      $ukey = $app_settings_model->get($key, 'ukey');

//      $cash = $app_settings_model->get($key, 'cash');
      $cheque = $app_settings_model->get($key, 'cheque');
//      $card = $app_settings_model->get($key, 'card');

      $removeOrderSymbols = $app_settings_model->get($key, 'removeOrderSymbols');

      $self_days = $app_settings_model->get($key, 'self_days');
      $sms_sender = $app_settings_model->get($key, 'sms_sender');

      $site = $app_settings_model->get($key, 'site');

      $date = waRequest::request('axiomus_date');
      $from_time = waRequest::request('axiomus_from_time');
      $to_time = waRequest::request('axiomus_to_time');
      $orderID = waRequest::request('orderID');

      $shipping_type = waRequest::request('shipping_type');
      $office_code = waRequest::request('office_code');
      $office_info = waRequest::request('office_info');
      $sms = waRequest::request('sms');

      $valuation = waRequest::request('valuation') ? 'yes' : 'no';
      $cash = waRequest::request('cash') ? 'yes' : 'no';
      $card = waRequest::request('card') ? 'yes' : 'no';
      $post_tarif = waRequest::request('post_tarif') ? 'yes' : 'no';
      $fragile = waRequest::request('fragile') ? 'yes' : 'no';
      $optimize = waRequest::request('optimize') ? 'yes' : 'no';
      $class1 = waRequest::request('class1') ? 'yes' : 'no';
      $big = waRequest::request('big') ? 'yes' : 'no';
      $insurance = waRequest::request('insurance') ? 'yes' : 'no';
      $checkup = waRequest::request('checkup') ? 'yes' : 'no';
      $part_return = waRequest::request('part_return') ? 'yes' : 'no';
      $waiting = waRequest::request('waiting') ? 'yes' : 'no';

      $msk_region = $app_settings_model->get($key, 'msk_region');
      $msk_region_city = $app_settings_model->get($key, 'msk_region_city');
      $piter_region = $app_settings_model->get($key, 'piter_region');
      $piter_region_city = $app_settings_model->get($key, 'piter_region_city');
      $add_product_code = $app_settings_model->get($key, 'add_product_code');
      $default_weight = $app_settings_model->get($key, 'default_weight');

      $post_type = $app_settings_model->get($key, 'post_type');
      $wrap_type = $app_settings_model->get($key, 'wrap_type');;

      $dpd_post_type = $app_settings_model->get($key, 'dpd_post_type');

      $dpd_street = waRequest::request('dpd_street');
      $dpd_house = waRequest::request('dpd_house');
      $dpd_building = waRequest::request('dpd_building');
      $dpd_apartmen = waRequest::request('dpd_apartmen');

      $order_model = new shopOrderModel();
      $order = $order_model->getById($orderID);

      if($order['axiomus_orderID']!=''){

        $this->response = [
          'status' => 'ok',
          'axiomusResultID' => $order['axiomus_orderID'],
        ];

        return;
      }

      $order_params_model = new shopOrderParamsModel();
      $order['params'] = $order_params_model->get($orderID);
      $order_items_model = new shopOrderItemsModel();
      $order['items'] = $order_items_model->getByField('order_id', $orderID, true);

      // get items weight
      $items = [];
      $product_ids = [];
      foreach ($order['items'] as $item_id => $item) {
        if ($item['type'] == 'product') {
           $items[$item['id']] = [
             'product_id' => $item['product_id'],
             'sku_id' => $item['sku_id'],
             'quantity' => $item['quantity']
           ];
           $product_ids[] = $item['product_id'];
        }
      }

      $feature_model = new shopFeatureModel();
      $f = $feature_model->getByCode('weight');
      if($f) {
        $product_features_model = new shopProductFeaturesModel();
        $sql = "SELECT product_id, sku_id, feature_value_id FROM ".$product_features_model->getTableName()."
                WHERE feature_id = i:0 AND product_id IN (i:1)";
        $product_values = $sku_values = [];
        $rows = $product_features_model->query($sql, $f['id'], array_unique($product_ids))->fetchAll();
        if ($rows) {
          foreach ($rows as $row) {
            if ($row['sku_id']) {
                $sku_values[$row['sku_id']] = $row['feature_value_id'];
            } else {
                $product_values[$row['product_id']] = $row['feature_value_id'];
            }
          }
        }
        $model = shopFeatureModel::getValuesModel($f['type']);
        $values = $model->getValues('id', array_unique(array_merge($product_values, $sku_values)));
        $unit = false;
        foreach ($items as $item_id => $item) {
            $sku_id = $item['sku_id'];
            $product_id = $item['product_id'];
            if (!empty($sku_values[$sku_id])) {
                $v_id = $sku_values[$sku_id];
                $v = $values[$f['id']][$v_id];
                $items[$item_id] = $v->value_base_unit;
                $items[$item_id] = (string)$v;
            } elseif (!empty($product_values[$product_id])) {
                $v_id = $product_values[$product_id];
                $v = $values[$f['id']][$v_id];
                $items[$item_id] = $v->value_base_unit;
            } else {
                unset($items[$item_id]);
            }
        }
        foreach ($order['items'] as $key => $item) {
          if(isset($items[$item['id']]))
            $order['items'][$key]['weight'] = str_replace(',','.',(string)$items[$item['id']]);
        }
      }

      $customer_contact = new waContact($order['contact_id']);

      $contact_model = new waContactModel();
      $order['customer'] = $contact_model->getByField('id', $order['contact_id']);

      // Customer info
      $main_contact_info = [];
      foreach (['email', 'phone', 'im'] as $f) {
          if ( ( $v = $customer_contact->get($f, 'top,html'))) {
              $main_contact_info[] = [
                'id' => $f,
                'name' => waContactFields::get($f)->getName(),
                'value' => is_array($v) ? implode(', ', $v) : $v,
              ];
          }
      }
      $order['customer']['info'] = $main_contact_info;

      $itemsCount = 0;
      $items = '<items>';
      foreach($order['items'] as $key => $item) {

        $weight = '0.01';
        if(isset($item['weight']) && $item['weight']!=0){
          $weight = $item['weight'];
        }

        if($default_weight>0){
          $weight = $default_weight;
        }

        if($add_product_code && $item['sku_code']!=''){
          $item['name'] = "[".$item['sku_code']."] ".$item['name'];
        }

        $items .= '<item name="'.$this->axiEscape($item['name']).'" weight="'.$weight.'" quantity="'.$item['quantity'].'" price="'.number_format($item['price'],2,'.','').'" />';
        $itemsCount += $item['quantity'];
      }
      $items .= '</items>';

      $discount = '';
      if($order['discount']>0){

        $discount = 'discount_value="'.number_format($order['discount'],2,'.','').'" discount_unit="1"';
      }

      $order_amount = str_replace(',','.',(float)$order['total']);

      $phone = '';
      $email = '';
      foreach($order["customer"]['info'] as $value){
        if($value['id']=='phone'){
          $phone = preg_replace('~[^0-9]+~','',$value['value']);
          if($phone[0]=="7" && $phone[1]!='8') $phone = substr($phone,1);
          if($phone[0]=="8") $phone = substr($phone,1);
          if(strlen($phone)==10) $phone = '7'.$phone;
          if(strlen($phone)<10) $phone = '';
        }
        if($value['id']=='email'){
          $email = strtolower(strip_tags($value['value']));
        }
      }

      $order['orderID_view'] = shopHelper::encodeOrderId($order['id']);
      $order['orderID_view'] = str_replace($removeOrderSymbols, '', $order['orderID_view']);

      $city = 1;
      if($order['params']['shipping_address.city']=='Москва') $city = 0;
      if(
          ($msk_region!='' && $order['params']['shipping_address.region']==$msk_region) ||
          ($msk_region_city!='' && $order['params']['shipping_address.region']==$msk_region_city)) $city = 0;

      if(
          ($piter_region!='' && $order['params']['shipping_address.region']==$piter_region) || 
          ($piter_region_city!='' && $order['params']['shipping_address.region']==$piter_region_city)) $city = 1;

      if($shipping_type=='courier'){

        $xml = '<?xml version="1.0" standalone="yes"?>
          <singleorder>
          <mode>new</mode>
          <auth ukey="'.$ukey.'" checksum="'.md5($uid.'u'.count($order['items']).$itemsCount).'" />
          <order inner_id="'.$order['orderID_view'].'" 
                 name="'.$this->axiEscape($order['customer']['firstname']).' '.$this->axiEscape($order['customer']['lastname']).'" 
                 address="'.$this->axiEscape(str_replace('"',"'",$order['params']['shipping_address.street'])).'" 
                 from_mkad="0" d_date="'.$date.'" b_time="'.$from_time.'" e_time="'.$to_time.'" 
                 incl_deliv_sum="'.number_format($order['shipping'],2,'.','').'" places="1" 
                 city="'.$this->axiEscape($city).'" '.($sms && $phone!=''?'sms="'.$this->axiEscape($phone).'"':'').' '.($sms && $sms_sender!='' && $phone!=''?'sms_sender="'.$this->axiEscape($sms_sender).'"':'').' '.$discount.' 
                 site="'.$site.'" '.($email ? 'email="'.$this->axiEscape($email).'"' : '').'>
             <contacts>'.$this->axiEscape($phone).'</contacts>
             <description>'.$this->axiEscape($order['comment']).'</description>
             <hidden_desc></hidden_desc>
             <services cash="'.$cash.'" 
                       cheque="'.$cheque.'" 
                       card="'.$card.'" 
                       big="'.$big.'" />
             '.$items.'
          </order>
          </singleorder>';

      } elseif($shipping_type=='self'){

        $edate = explode('-',$date);
        $edate = mktime(0,0,0,$edate[1],$edate[2]+$self_days,$edate[0]);
        $edate = date('Y-m-d', $edate);

        $xml = '<?xml version="1.0" standalone="yes"?>
          <singleorder>
          <mode>new_boxberry_pickup</mode>
          <auth ukey="'.$ukey.'" checksum="'.md5($uid.'u'.count($order['items']).$itemsCount).'" />
          <order inner_id="'.$order['orderID_view'].'" 
                 name="'.$this->axiEscape($order['customer']['firstname']).' '.$this->axiEscape($order['customer']['lastname']).'" 
                 office="'.$office_code.'" d_date="'.$edate.'" incl_deliv_sum="'.number_format($order['shipping'],2,'.','').'" 
                 places="1" '.($sms && $phone!=''?'sms="'.$this->axiEscape($phone).'"':'').' '.($sms && $sms_sender!='' && $phone!=''?'sms_sender="'.$this->axiEscape($sms_sender).'"':'').' '.$discount.' 
                 avoid_part_return="0" site="'.$site.'" '.($email ? 'email="'.$this->axiEscape($email).'"' : '').'>
             <address office_code="'.$office_code.'" />
             <contacts>'.$this->axiEscape($phone).'</contacts>
             <description>'.$this->axiEscape($order['comment']).'</description>
             <services cod="'.$cash.'" 
                       checkup="'.$checkup.'" 
                       part_return="'.$part_return.'" />
             '.$items.'
          </order>
          </singleorder>';

      } elseif($shipping_type=='post'){

        $region_model = new waRegionModel(); 
        $regionName = '';
        if ($region = $region_model->get($order['params']['shipping_address.country'], $order['params']['shipping_address.region'])) {
          $regionName = $region['name'];
        }

        $xml = '<?xml version="1.0" standalone="yes"?>
          <singleorder>
          <mode>new_post</mode>
          <auth ukey="'.$ukey.'" />
          <order inner_id="'.$order['orderID_view'].'" 
                 name="'.$this->axiEscape($order['customer']['firstname']).' '.$this->axiEscape($order['customer']['lastname']).'" 
                 b_date="'.$date.'" incl_deliv_sum="'.number_format($order['shipping'],2,'.','').'" post_type="'.$post_type.'" 
                 site="'.$site.'" '.($email ? 'email="'.$this->axiEscape($email).'"' : '').' '.($sms && $phone!=''?'sms="'.$this->axiEscape($phone).'"':'').' 
                 wrap_type="'.$wrap_type.'">
             <address index="'.$this->axiEscape(str_replace('"',"'",$order['params']['shipping_address.zip'])).'" 
                      region="'.$this->axiEscape($regionName).'" 
                      area="'.$this->axiEscape(str_replace('"',"'",$order['params']['shipping_address.city'])).'" 
                      p_address="'.$this->axiEscape(str_replace('"',"'",$order['params']['shipping_address.street'])).'" />
             <contacts>'.($sms ? ''.$this->axiEscape($phone).'' : '').'</contacts>
             <services valuation="'.$valuation.'" 
                       cod="'.$cash.'" 
                       post_tarif="'.$post_tarif.'" 
                       fragile="'.$fragile.'" 
                       optimize="'.$optimize.'" 
                       class1="'.$class1.'" 
                       big="'.$big.'" 
                       sms_inform="'.($sms?'yes':'no').'" 
                       insurance="'.$insurance.'" />
             '.$items.'
          </order>
          </singleorder>';

      } elseif($shipping_type=='dpd'){

        $xml = '<?xml version="1.0" standalone="yes"?>
          <singleorder>
          <mode>new_dpd</mode>
          <auth ukey="'.$ukey.'" />
          <order inner_id="'.$order['orderID_view'].'" 
                 name="'.$this->axiEscape($order['customer']['firstname']).' '.$this->axiEscape($order['customer']['lastname']).'" 
                 b_date="'.$date.'" b_time="'.$from_time.'" e_time="'.$to_time.'" incl_deliv_sum="'.number_format($order['shipping'],2,'.','').'" 
                 post_type="'.$dpd_post_type.'" country="0"
                 site="'.$site.'" '.($email ? 'email="'.$this->axiEscape($email).'"' : '').'>
             <address carrymode="'.$this->axiEscape($office_code).'" />
             <contacts>'.$this->axiEscape($phone).'</contacts>
             <services valuation="'.$valuation.'" 
                       cod="'.$cash.'" 
                       post_tarif="'.$post_tarif.'" 
                       fragile="'.$fragile.'" 
                       optimize="'.$optimize.'" 
                       class1="'.$class1.'" 
                       big="'.$big.'" 
                       sms_inform="'.$sms.'" 
                       insurance="'.$insurance.'" />
             '.$items.'
          </order>
          </singleorder>';

      } elseif($shipping_type=='dpd_courier'){

        $region_model = new waRegionModel(); 
        $regionName = '';
        if ($region = $region_model->get($order['params']['shipping_address.country'], $order['params']['shipping_address.region'])) {
          $regionName = $region['name'];
        }

        $xml = '<?xml version="1.0" standalone="yes"?>
          <singleorder>
          <mode>new_dpd</mode>
          <auth ukey="'.$ukey.'" />
          <order inner_id="'.$order['orderID_view'].'" 
                 name="'.$this->axiEscape($order['customer']['firstname']).' '.$this->axiEscape($order['customer']['lastname']).'" 
                 b_date="'.$date.'" b_time="'.$from_time.'" e_time="'.$to_time.'" incl_deliv_sum="'.number_format($order['shipping'],2,'.','').'" 
                 post_type="'.$dpd_post_type.'" country="0"
                 site="'.$site.'" '.($email ? 'email="'.$this->axiEscape($email).'"' : '').'>
             <address index="'.$this->axiEscape(str_replace('"',"'",$order['params']['shipping_address.zip'])).'" 
                      region="'.$this->axiEscape($regionName).'" 
                      area="'.$this->axiEscape(str_replace('"',"'",$order['params']['shipping_address.city'])).'" 
                      street="'.$this->axiEscape($dpd_street).'" 
                      house="'.$this->axiEscape($dpd_house).'" 
                      building="'.$this->axiEscape($dpd_building).'" 
                      apartmen="'.$this->axiEscape($dpd_apartmen).'" />
             <contacts>'.$this->axiEscape($phone).'</contacts>
             <services valuation="'.$valuation.'" 
                       cod="'.$cash.'" 
                       fragile="'.$fragile.'" 
                       big="'.$big.'" 
                       waiting="'.$waiting.'" />
             '.$items.'
          </order>
          </singleorder>';
      }

//      echo $uid.' '.count($order['items']).' '.$itemsCount.' '.$order_amount.' '.$date.' '.$from_time.'no/no/no';
//      echo $xml;die();

      $axiPlugin = new shopAxiomusordersPlugin(true);
      $curlRes = $axiPlugin->sendXml($xml);
      $result = false;
      if($curlRes['error']=='' && $curlRes['result']!=''){

         $result = simplexml_load_string( $curlRes['result'] );
      }

      if($curlRes['error']!=''){

        $this->response = [
          'status' => 'error',
          'error' => $curlRes['error'],
        ];

        return false;

      } elseif(!$result){

        $xmlError = "Failed loading XML<br />";
        foreach(libxml_get_errors() as $error) {
          $xmlError .= "<br />".$error->message;
        }

        $this->response = [
          'status' => 'error',
          'error' => $xmlError,
        ];

        return false;

      } elseif((isset($result->status) && !isset($result->auth)) || !strstr($curlRes['result'], 'code="0"')){

        $this->response = [
          'status' => 'error',
          'error' => (string)$result->status,
        ];

        return false;

      }

      // save axiomus orderID to order
      preg_match_all('/objectid="(.*)">/i', $curlRes['result'], $out);
      $axiomus_orderID = $out[1][0];

      $order_model->updateById($orderID, [
        'axiomus_orderID' => $out[1][0], 
        'axiomus_okey' => (string)$result->auth
      ]);

      // add log to order history
      $order = $order_model->getById($orderID);
      $order_log_model = new shopOrderLogModel();
      $order_log_model->add([
        'order_id' => $orderID,
        'action_id' => '',
        'text' => 'Заказ был успешно выгружен в Axiomus, номер: '.$axiomus_orderID.($shipping_type=='self'?', пункт самовывоза: '.$office_info:''),
        'before_state_id' => $order['state_id'],
        'after_state_id' => $order['state_id'],
      ]);

      $this->response = [
        'status' => 'ok',
        'axiomusResultID' => $axiomus_orderID,
      ];

      return;
  }

  function axiEscape( $str, $strip_tags = true ){

    if($strip_tags){
       $str = strip_tags($str);
    }
    $str = str_replace('&nbsp;', ' ', $str);
    $str = str_replace( "&", "&amp;", $str );
    $str = str_replace( "<", "&lt;", $str );
    $str = str_replace( ">", "&gt;", $str );
    $str = str_replace( "\"", "&quot;", $str );
    $str = str_replace( "'", "&apos;", $str );
    $str = str_replace( "\r", "", $str );
    return $str;
  }

}
