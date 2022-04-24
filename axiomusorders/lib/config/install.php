<?php

$model = new waModel();

try {
    $model->query('SELECT `axiomus_orderID` FROM `shop_order` WHERE 0');
} catch (waDbException $e) {
    $model->exec('ALTER TABLE `shop_order` ADD `axiomus_orderID` VARCHAR(1000) NOT NULL DEFAULT "", ADD `axiomus_okey` VARCHAR(1000) NOT NULL DEFAULT ""');
}
