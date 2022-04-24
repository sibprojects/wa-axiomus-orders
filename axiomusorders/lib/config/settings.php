<?php

return array(
    'uid' => array(
        'title' => 'Axiomus Uid',
        'value' => '',
        'description' => 'Тестовый Uid: 92',
        'control_type' => waHtmlControl::INPUT
    ),
    'ukey' => array(
        'title' => 'Axiomus Ukey',
        'value' => '',
        'description' => 'Тестовый Ukey: XXcd208495d565ef66e7dff9f98764XX',
        'control_type' => waHtmlControl::INPUT
    ),
    'shipping_type_courier' => array(
        'title' => 'Доставка курьером',
        'description'  => 'Выберите если хотите использовать этот вариант доставки',
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'shipping_type_self' => array(
        'title' => 'Самовывоз из пвз БоксБерри',
        'description'  => 'Выберите если хотите использовать этот вариант доставки',
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'shipping_type_post' => array(
        'title' => 'Доставка Почтой России',
        'description'  => 'Выберите если хотите использовать этот вариант доставки. <br /><span style="color:red;">Внимание! Для данного метода доставки у вас должен быть включен и использоваться "индекс" покупателя в адресе доставки!</span>',
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'shipping_type_dpd' => array(
        'title' => 'Доставка DPD Самовывоз',
        'description'  => 'Выберите если хотите использовать этот вариант доставки',
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'shipping_type_dpd_courier' => array(
        'title' => 'Доставка DPD Курьер',
        'description'  => 'Выберите если хотите использовать этот вариант доставки',
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'post_type' => array(
        'title' => 'Тип отправления Почтой России',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              '1' => 'Посылка',
                              '2' => 'Посылка 1 Класс',
                              '3' => 'Посылка Онлайн',
                              '4' => 'Курьер Онлайн',
                     )
    ),
    'wrap_type' => array(
        'title' => 'Тип упаковки отправления Почтой России',
        'value' => '2',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              '1' => 'Без доупаковки',
                              '2' => 'Без доупаковки (негабарит)',
                              '3' => 'Упаковка',
                              '4' => 'Упаковка (негабарит)',
                     )
    ),
    'msk_region' => array(
        'title' => 'Московская область',
        'value' => '',
        'description' => 'Можете оставить пустой - тогда будет применен фильтр по городу "Москва"',
        'control_type'     => waHtmlControl::SELECT,
        'options_callback' => array('shopAxiomusordersPlugin', 'zones'),
    ),
    'msk_region_city' => array(
        'title' => 'Регион для города Москва',
        'value' => '',
        'description' => 'Укажите если у вас задана область для города Москва',
        'control_type'     => waHtmlControl::SELECT,
        'options_callback' => array('shopAxiomusordersPlugin', 'zones'),
    ),
    'piter_region' => array(
        'title' => 'Ленинградская область',
        'value' => '',
        'description' => 'Можете оставить пустой - тогда будет применен фильтр по любому городу не "Москва"',
        'control_type'     => waHtmlControl::SELECT,
        'options_callback' => array('shopAxiomusordersPlugin', 'zones'),
    ),
    'piter_region_city' => array(
        'title' => 'Регион для города Санкт-Петербург',
        'value' => '',
        'description' => 'Укажите если у вас задана область для города Санкт-Петербург',
        'control_type'     => waHtmlControl::SELECT,
        'options_callback' => array('shopAxiomusordersPlugin', 'zones'),
    ),
    'msk_pvz_default' => array(
        'title' => 'Код ПВЗ для Москвы по умолчанию',
        'value' => '',
        'description' => 'Можете оставить пустым - тогда будет выбран первый ПВЗ из списка',
        'control_type' => waHtmlControl::INPUT
    ),
    'piter_pvz_default' => array(
        'title' => 'Код ПВЗ для Санкт-Петербурга по умолчанию',
        'value' => '',
        'description' => 'Можете оставить пустым - тогда будет выбран первый ПВЗ из списка',
        'control_type' => waHtmlControl::INPUT
    ),
    'cash' => array(
        'title' => 'Наложенный платеж',
        'value' => 'yes',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'cheque' => array(
        'title' => 'Чек по агентскому договору',
        'value' => 'yes',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'card' => array(
        'title' => 'Оплата по пластиковой карте',
        'description'  => 'Не допускается для заявок в Санкт-Петербург',
        'value' => 'yes',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'valuation' => array(
        'title' => 'Объявленная стоимость',
        'description'  => 'Только для отправки Почтой России',
        'value' => 'yes',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'post_tarif' => array(
        'title' => 'Наложенный платеж + почтовый тариф',
        'description'  => 'Только для отправки Почтой России',
        'value' => 'no',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'fragile' => array(
        'title' => 'Осторожно! (только для тарифа "Посылка")',
        'description'  => 'Только для отправки Почтой России',
        'value' => 'no',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'optimize' => array(
        'title' => 'Оптимизатор тарифа',
        'description'  => 'Только для отправки Почтой России',
        'value' => 'no',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'class1' => array(
        'title' => 'Строгий тип соответствия',
        'description'  => 'Только для отправки Почтой России',
        'value' => 'no',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'big' => array(
        'title' => 'Большегруз',
        'description'  => 'Только для отправки Почтой России',
        'value' => 'no',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'insurance' => array(
        'title' => 'Расширенное страхование',
        'description'  => 'Только для отправки Почтой России',
        'value' => 'no',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              'yes' => 'Да',
                              'no' => 'Нет',
                     )
    ),
    'dpd_post_type' => array(
        'title' => 'Тип отправления DPD',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
                              '1' => 'DPD Online Express',
                              '2' => 'DPD Online Classic',
                     )
    ),
    'removeOrderSymbols' => array(
        'title' => 'Обрезать символы в номере заказа',
        'description'  => 'Укажите здесь те символы, которые необходимо удалить в номере заказа при его передаче в Аксиомус',
        'value' => '',
        'control_type' => waHtmlControl::INPUT
    ),
    'self_days' => array(
        'title' => 'Кол-во дней хранения заказа в пункте самовывоза',
        'value' => '5',
        'description' => '',
        'control_type' => waHtmlControl::INPUT
    ),
    'sms' => array(
        'title' => 'Отправлять sms-уведомления',
        'description'  => 'Номер покупателя будет считаться допустимым, если он начинается с 79, исключая номера, начинающиеся с 7940',
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'sms_sender' => array(
        'title' => 'Наименование отправителя sms-уведомлений',
        'description'  => 'Буквенно-цифровое значение минимум 3, максимум 11 символов. Поддерживаются латинские буквы a-zA-Z, цифры 0-9, дефис и точка.',
        'value' => '',
        'control_type' => waHtmlControl::INPUT
    ),
    'site' => array(
        'title' => 'Урл магазина',
        'value' => '',
        'description' => 'Урл вашего интернет магазина. Если оставить пустым и при этом в ЛК в карточке интернет магазина в разделе Для покупателей задано значение поля Сайт, то это значение бует подставлено в заявку автоматически',
        'control_type' => waHtmlControl::INPUT
    ),
    'add_product_code' => array(
        'title' => 'Переносить артикулы товаров',
        'description'  => 'Артикулы товаров будут добавляться в названия товаров в виде: [артикул_товара] название товара',
        'value' => 0,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'answer_sec' => array(
        'title' => 'Кол-во секунд ожидания ответа сервера Аксиомуса',
        'value' => '15',
        'description' => '',
        'control_type' => waHtmlControl::INPUT
    ),
    'pvz_select' => array(
        'title' => 'Выбор ПВЗ в виде выпадающего списка',
        'description'  => '',
        'value' => 0,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'show_form_on_button' => array(
        'title' => 'Включить кнопку "Выгрузить в Аксиомус"',
        'description'  => 'Выберите если хотите чтобы форма выгрузки была скрыта и отображалась только после нажатия кнопки',
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'default_weight' => array(
        'title' => 'Принудительный вес товара',
        'value' => '0',
        'description' => 'Укажите число больше 0 чтобы включить',
        'control_type' => waHtmlControl::INPUT
    ),
);