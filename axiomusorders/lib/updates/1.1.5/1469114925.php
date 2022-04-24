<?php
$model = new waModel();
try {
  $model->query('SELECT `axiomus_form_hidden` FROM `shop_order` WHERE 0');
} catch (waDbException $e) {
  $model->exec('ALTER TABLE `shop_order` ADD `axiomus_form_hidden` TINYINT(1) NOT NULL DEFAULT 0');
}
