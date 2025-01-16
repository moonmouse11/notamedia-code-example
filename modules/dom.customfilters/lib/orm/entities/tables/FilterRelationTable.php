<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\ORM\Entities\Tables;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\SystemException;

final class FilterRelationTable extends DataManager
{
    /**
     * @description Return table name
     *
     * @inheritDoc
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'dom_cf_filter_relation';
    }

    /**
     * @description Return table fields
     *
     * @inheritDoc
     *
     * @return array
     *
     * @throws SystemException
     */
    public static function getMap(): array
    {
        return [
            'id' => new IntegerField(
                name: 'id',
                parameters: [
                    'primary' => true,
                    'unique' => true,
                    'autocomplete' => true,
                ]
            ),
            'filter_id' => new StringField(
                name: 'filter_id',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                    'size' => 150,
                ]
            ),
            'filter_type' => new StringField(
                name: 'filter_type',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                    'size' => 150,
                ]
            ),
            'entity_type' => new StringField(
                name: 'entity_type',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                    'size' => 100,
                ]
            ),
            'entity_id' => new IntegerField(
                name: 'entity_id',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                ]
            ),
        ];
    }
}
