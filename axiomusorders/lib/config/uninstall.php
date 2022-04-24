<?php

$model = new waModel();

try {
   $model->exec('ALTER TABLE `shop_order` DROP `axiomus_orderID`, DROP `axiomus_okey`');
} catch(waDbException $e) {
    waLog::log('Unable to delete shop_axiomusorders_plugin fields from database.');
}
