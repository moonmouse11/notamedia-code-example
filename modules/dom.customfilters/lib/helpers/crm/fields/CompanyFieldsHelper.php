<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Crm\Fields;

use Bitrix\Crm\Service\Container;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserField\Types\EnumType;
use CCrmOwnerType;
use DomDigital\CustomFilters\Interfaces\Crm\EntityFieldsInterface;

final class CompanyFieldsHelper implements EntityFieldsInterface
{
    public const ENTITY_TYPE_ID = CCrmOwnerType::Company;

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getEntityFieldsMap(): array
    {
        $factory = Container::getInstance()->getFactory(entityTypeId: self::ENTITY_TYPE_ID);

        return array_map(
            callback: static fn($item) => [
                'id' => $item['USER_FIELD']['ID'],
                'title' => $item['TITLE'],
                'code' => $item['USER_FIELD']['FIELD_NAME'],
                'field_type' => $item['USER_FIELD']['USER_TYPE_ID'],
                'entity_id' => $item['USER_FIELD']['ENTITY_ID'],
                'values' => self::getValues(userFiled: $item),
            ],
            array: $factory?->getFieldsCollection()->toArray()
        );
    }

    private static function getEnumerationValues(array $userFiled): array
    {
        return array_map(
            callback: static fn($item) => [
                'id' => $item['ID'],
                'name' => $item['VALUE'],
                'user_field_id' => $item['USER_FIELD_ID'],
            ],
            array: EnumType::getList(userField: $userFiled)->arResult
        );
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function getIBlockValues(array $userFiled): array
    {
        return array_map(
            callback: static fn($item) => [
                'id' => $item['ID'],
                'name' => $item['NAME'],
            ],
            array: ElementTable::getList(
                parameters: [
                    'select' => ['ID', 'NAME'],
                    'filter' => [
                        'IBLOCK_ID' => $userFiled['SETTINGS']['IBLOCK_ID'],
                    ]
                ]
            )->fetchAll()
        );
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function getValues(array $userFiled): array
    {
        if ($userFiled['USER_FIELD']['USER_TYPE_ID'] === 'enumeration') {
            return self::getEnumerationValues(userFiled: $userFiled['USER_FIELD']);
        }

        if ($userFiled['USER_FIELD']['USER_TYPE_ID'] === 'iblock_element') {
            return self::getIBlockValues(userFiled: $userFiled);
        }


        return [];
    }

}