<?php
$model = new waModel();
try {
  $model->query('SELECT `axiomus_okey` FROM `shop_order` WHERE 0');
} catch (waDbException $e) {
  $model->exec('ALTER TABLE `shop_order` ADD `axiomus_okey` VARCHAR(1000) NOT NULL DEFAULT ""');
}
try {
  // remove calendar category
  $file = wa('shop')->getAppPath('plugins/axiomusorders/js');
  waFiles::delete($file);
} catch (Exception $e) {
  waLog::log('shop/plugins/axiomusorders: unable to delete calendar category.');
}

