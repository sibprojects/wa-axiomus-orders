<?php
return array(
    'name' => 'Выгрузка заказов в Axiomus',
    'description' => 'Курьерская и самовывоз',
    'vendor'=>'667100',
    'version'=>'2.0.0',
    'img' => 'img/icon.png',
    'icons' => array(
        16 => 'img/icon.png',
        24 => 'img/icon24.png',
        48 => 'img/icon48.png',
        96 => 'img/icon96.png',
    ),
    'shop_settings' => false,
    'frontend' => false,
    'handlers' => array(
      'backend_order'=>'backend_order',
    ),
);
//EOF
