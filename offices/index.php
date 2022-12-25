<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->SetTitle('Список офисов');

$APPLICATION->IncludeComponent(
    'gazprom:offices',
    '',
    array(
        'CACHE_TYPE' => 'A',
        'CACHE_TIME' => 60 * 60 * 24 * 30,
    )
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');