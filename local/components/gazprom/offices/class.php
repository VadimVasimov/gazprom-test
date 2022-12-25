<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class Offices extends \CBitrixComponent
{
    public string $iblock_type = 'content';
    public string $iblock_code = 'offices';
    public string $iblock_name = 'Офисы';

    private string $site_id = 's1'; // хардкод, так как в админке константа SITE_ID возвращает не id сайта, а id языка

    private array $iblock_properties = array(
        'PHONE' => array(
            'ACTIVE' => 'Y',
            'SORT' => 100,
            'NAME' => 'Телефон',
            'CODE' => 'PHONE',
            'PROPERTY_TYPE' => 'S',
        ),
        'EMAIL' => array(
            'ACTIVE' => 'Y',
            'SORT' => 200,
            'NAME' => 'Email',
            'CODE' => 'EMAIL',
            'PROPERTY_TYPE' => 'S',
        ),
        'CITY' => array(
            'ACTIVE' => 'Y',
            'SORT' => 300,
            'NAME' => 'Город',
            'CODE' => 'CITY',
            'PROPERTY_TYPE' => 'S',
        ),
        'COORDINATES' => array(
            'IS_REQUIRED' => 'Y',
            'ACTIVE' => 'Y',
            'SORT' => 400,
            'NAME' => 'Координаты',
            'CODE' => 'COORDINATES',
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'map_yandex',
        ),
    );

    public function __construct($component = null)
    {
        parent::__construct($component);

        CModule::IncludeModule('iblock');
    }

    public function executeComponent()
    {
        if ($this->startResultCache()) {
            $dbOffices = CIBlockElement::GetList(
                array('SORT' => 'ASC'),
                array('IBLOCK_ID' => $this->getOfficesInfoBlockId(), 'ACTIVE' => 'Y'),
                false,
                false,
                array('ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_PHONE', 'PROPERTY_EMAIL', 'PROPERTY_CITY', 'PROPERTY_COORDINATES')
            );
            while ($office = $dbOffices->Fetch()) {
                $this->arResult['OFFICES'][] = array(
                    'NAME' => $office['NAME'],
                    'PHONE' => $office['PROPERTY_PHONE_VALUE'],
                    'EMAIL' => $office['PROPERTY_EMAIL_VALUE'],
                    'CITY' => $office['PROPERTY_CITY_VALUE'],
                    'COORDINATES' => $office['PROPERTY_COORDINATES_VALUE'],
                );
            }

            $this->includeComponentTemplate();
        }
    }

    /**
     * Метод создания инфоблока (тип, инфоблок, свойства)
     *
     * @return int - ID инфоблока
     */
    public function addOfficesInfoBlockIfNotExist(): int
    {
        if (!$this->checkInfoBlockTypeIsExist()) {
            $this->addOfficesInfoBlockType();
        }

        if (!$iblockId = $this->getOfficesInfoBlockId()) {
            $iblockId = $this->addOfficesInfoBlock();
        }

        foreach ($this->iblock_properties as $propertyCode => $property) {
            if (!$this->checkInfoBlockPropertyIsExist($propertyCode)) {
                $this->addOfficesInfoBlockProperty($property);
            }
        }

        return $iblockId;
    }

    /**
     * Метод проверки существования типа инфоблока
     *
     * @return bool
     */
    private function checkInfoBlockTypeIsExist(): bool
    {
        $dbIblockType = CIBlockType::GetList(array(), array('ID' => $this->iblock_type));

        return (bool)$dbIblockType->SelectedRowsCount();
    }

    /**
     * Метод создания типа инфоблока
     *
     * @return bool
     */
    private function addOfficesInfoBlockType(): bool
    {
        $iblockType = new CIBlockType;

        return $iblockType->Add(array(
            'ID' => $this->iblock_type,
            'SORT' => 100,
            'LANG'  => array(
                'ru' => array(
                    'NAME' => 'Контент',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы'
                ),
                'en' => array(
                    'NAME' => 'Content',
                    'SECTION_NAME' => 'Sections',
                    'ELEMENT_NAME' => 'Elements'
                ),
            )
        ));
    }

    /**
     * Метод получения ID инфоблока (с кэшированием)
     *
     * @return int
     */
    public function getOfficesInfoBlockId(): int
    {
        $iblockId = 0;

        $obCache = new \CPHPCache;
        $cacheId = $this->iblock_type . $this->iblock_code;
        if ($obCache->InitCache($this->arParams['CACHE_TIME'], $cacheId, 'iblock_id')) {
            $iblockId = (int)$obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            $dbIblock = CIBlock::GetList(
                array(),
                array(
                    'SITE_ID' => $this->site_id,
                    'TYPE' => $this->iblock_type,
                    'CODE' => $this->iblock_code,
                    'CHECK_PERMISSIONS' => 'N',
                )
            );

            if ($iblock = $dbIblock->Fetch()) {
                $iblockId = $iblock['ID'];

                $obCache->EndDataCache($iblockId);
            }
        }

        return $iblockId;
    }

    /**
     * Метод создания инфоблока
     *
     * @return int
     */
    private function addOfficesInfoBlock(): int
    {
        $iblock = new CIBlock;

        return $iblock->Add(array(
            'ACTIVE' => 'Y',
            'NAME' => $this->iblock_name,
            'CODE' => $this->iblock_code,
            'IBLOCK_TYPE_ID' => $this->iblock_type,
            'VERSION'  => 2, // инфоблок 2.0
            'SITE_ID' => $this->site_id,
            'GROUP_ID' => array(
                '2' => 'R', // "чтение" инфоблока для всех пользователей, даже неавторизованных
            ),
        ));
    }

    /**
     * Метод проверки существования свойства инфоблока по символьному коду свойства
     *
     * @param string $propertyCode - символьный код свойства
     *
     * @return bool
     */
    private function checkInfoBlockPropertyIsExist(string $propertyCode): bool
    {
        $dbIblockProperty = CIBlockProperty::GetByID($propertyCode, $this->getOfficesInfoBlockId(), $this->iblock_code);

        return (bool)$dbIblockProperty->SelectedRowsCount();
    }

    /**
     * Метод создания свойства инфоблока
     *
     * @param $property - массив свойства инфоблока
     *
     * @return void
     */
    private function addOfficesInfoBlockProperty($property)
    {
        $property['IBLOCK_ID'] = $this->getOfficesInfoBlockId();

        $iblockProperty = new CIBlockProperty;

        $iblockProperty->Add($property);
    }

    /**
     * Метод создания элемента инфоблока
     *
     * @param $office - массив жлемента инфоблока
     *
     * @return void
     */
    public function addOffice($office)
    {
        $iblockElement = new CIBlockElement();

        $iblockElement->Add(array(
            'ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => false,
            'IBLOCK_ID' => $this->getOfficesInfoBlockId(),
            'NAME' => $office['Name'],
            'PROPERTY_VALUES'=> array(
                'PHONE' => $office['Phone'],
                'EMAIL' => $office['Email'],
                'COORDINATES' => $office['Coordinates'],
                'CITY' => $office['City'],
            ),
        ));
    }
}