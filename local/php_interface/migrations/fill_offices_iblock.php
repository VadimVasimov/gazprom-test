<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

CBitrixComponent::includeComponentClass('gazprom:offices');
$component = new \Offices();

$iblockId = $component->getOfficesInfoBlockId();

if ($iblockId) {
    $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'] . '/upload/xml/offices.xml');

    if (!$xml) {
        echo 'Не удалось загрузить XML файл "offices.xml" из директории /upload/xml/';
    } else {
        $offices = json_decode(json_encode($xml), true)['Office'];

        foreach ($offices as $office) {
            $component->addOffice($office);
        }
    }
} else {
    echo 'Инфоблок офисов отсутствует, запустите сначала миграцию создания инфоблока!';
}