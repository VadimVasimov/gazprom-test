<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

CBitrixComponent::includeComponentClass('gazprom:offices');
$component = new \Offices();

$iblockId = $component->addOfficesInfoBlockIfNotExist();

if ($iblockId) {
    echo 'ID инфоблока: ' . $iblockId;
} else {
    echo 'Не удалось создать инфоблок';
}