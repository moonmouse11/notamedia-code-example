<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\ORM\Entities\Tables;

use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\SystemException;

final class FilterTable extends DataManager
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
        return 'dom_cf_filters';
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
                    'autocomplete' => true,
                    'unique' => true
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
            'name' => new StringField(
                name: 'name',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                    'size' => 200,
                ]
            ),
            'option_name' => new StringField(
                name: 'option_name',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                    'size' => 200,
                ]
            ),
            'category' => new StringField(
                name: 'category',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                    'size' => 100,
                ]
            ),
            'author_id' => new IntegerField(
                name: 'author_id',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                ]
            ),
            'value' => new TextField(
                name: 'value',
                parameters: [
                    'nullable' => false,
                    'required' => true,
                ]
            ),
            'common' => new BooleanField(
                name: 'common',
                parameters: [
                    'required' => false,
                    'default' => false
                ]
            ),
            'soft_delete' => new BooleanField(
                name: 'soft_delete',
                parameters: [
                    'required' => false,
                    'default' => false
                ]
            )
        ];
    }
}