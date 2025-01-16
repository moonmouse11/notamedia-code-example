<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\ORM\Entities\Tables;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\SystemException;

final class AccessTable extends DataManager
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
        return 'dom_cf_accesses';
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
                    'column_name' => 'id',
                    'primary' => true,
                    'autocomplete' => true,
                    'unique' => true
                ]
            ),
            'role' => new StringField(
                name: 'role',
                parameters: [
                    'required' => false,
                    'nullable' => true,
                    'size' => 100,
                ]
            ),
            'entity_type' => new StringField(
                name: 'entity_type',
                parameters: [
                    'required' => true,
                    'nullable' => false,
                    'size' => 100,
                ]
            ),
            'entity_id' => new IntegerField(
                name: 'entity_id',
                parameters: [
                    'nullable' => false,
                    'required' => true
                ]
            ),
            'name' => new StringField(
                name: 'name',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                    'size' => 150,
                ]
            ),
            'active' => new BooleanField(
                name: 'active',
                parameters: [
                    'nullable' => false,
                    'required' => false,
                    'default' => true
                ]
            )
        ];
    }
}