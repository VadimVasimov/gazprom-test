<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->addExternalJS('https://api-maps.yandex.ru/2.1/?apikey=81e40c70-f934-48c4-b192-7302ed4a37cc&lang=ru_RU');?>

<div id="offices-map" class="map"></div>

<script type="text/javascript">
    let offices = <?=CUtil::PhpToJSObject($arResult['OFFICES'], false, true);?>;
</script>