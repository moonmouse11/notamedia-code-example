<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\ORM;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Option\OptionCategoryEnum;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Helpers\Option\FilterHelper;
use DomDigital\CustomFilters\ORM\Entities\Tables\FilterTable;
use Exception;

final class CustomFilterHelper
{
    /**
     * @description Return list of custom filters
     *
     * @param int|null $authorId
     * @param bool $softDelete
     *
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getList(int $authorId = null, bool $softDelete = false): array
    {
        if ($authorId === null) {
            return FilterTable::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        'soft_delete' => $softDelete,
                    ]
                ]
            )->fetchAll();
        }

        return FilterTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'author_id' => $authorId,
                    'soft_delete' => $softDelete,
                ]
            ]
        )->fetchAll();
    }

    /**
     * @description Return custom filter by id
     *
     * @param string $filterId
     *
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getById(string $filterId): array
    {
        return FilterTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'filter_id' => $filterId
                ]
            ]
        )->fetch();
    }

    /**
     * @description Create custom filter
     *
     * @param array $filter
     * @param OptionNameEnum $optionNameEnum
     * @param int $authorId
     * @param int|null $dealCategory
     * @param bool $common
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws Exception
     */
    public static function create(
        array $filter,
        OptionNameEnum $optionNameEnum,
        int $authorId,
        int $dealCategory = null,
        bool $common = false
    ): bool {
        return FilterTable::add(
            data: [
                'name' => $filter['name'],
                'filter_id' => $filter['id'],
                'value' => serialize(value: $filter),
                'category' => OptionCategoryEnum::UI_FILTER->value,
                'author_id' => $authorId,
                'option_name' => $optionNameEnum->getOptionName(dealCategory: $dealCategory),
                'common' => $common,
                'soft_delete' => false,
            ]
        )->isSuccess();
    }

    /**
     * @description Check if filter exists
     *
     * @param string $filterId
     *
     * @return bool
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function isExists(string $filterId): bool
    {
        return !empty(
        FilterTable::getRow(
            parameters: [
                'select' => ['id'],
                'filter' => [
                    'filter_id' => $filterId
                ]
            ]
        )['id']
        );
    }

    /**
     * @description Create or update custom filter
     *
     * @param array $filter
     * @param OptionNameEnum $optionNameEnum
     * @param int $authorId
     * @param int|null $dealCategory
     * @param bool $common
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function createOrUpdate(
        array $filter,
        OptionNameEnum $optionNameEnum,
        int $authorId,
        int $dealCategory = null,
        bool $common = false
    ): bool {
        if (self::isExists(filterId: $filter['id'])) {
            return self::update(
                filter: $filter,
                optionNameEnum: $optionNameEnum,
                authorId: $authorId,
                dealCategory: $dealCategory,
                common: $common
            );
        }

        return self::create(
            filter: $filter,
            optionNameEnum: $optionNameEnum,
            authorId: $authorId,
            dealCategory: $dealCategory,
            common: $common
        );
    }

    /**
     * @description Update custom filter
     *
     * @param array $filter
     * @param OptionNameEnum $optionNameEnum
     * @param int $authorId
     * @param int|null $dealCategory
     * @param bool $common
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function update(
        array $filter,
        OptionNameEnum $optionNameEnum,
        int $authorId,
        int $dealCategory = null,
        bool $common = false
    ): bool {
        $primary = FilterTable::getRow(
            parameters: [
                'select' => ['id'],
                'filter' => [
                    'filter_id' => $filter['id']
                ]
            ]
        )['id'];

        return FilterTable::update(
            primary: $primary,
            data: [
                'name' => $filter['name'],
                'filter_id' => $filter['id'],
                'author_id' => $authorId,
                'value' => serialize(value: $filter),
                'category' => OptionCategoryEnum::UI_FILTER->value,
                'option_name' => $optionNameEnum->getOptionName(dealCategory: $dealCategory),
                'common' => $common,
                'soft_delete' => false,
            ]
        )->isSuccess();
    }

    /**
     * @description Update custom filter
     *
     * @param string $filterId
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function delete(string $filterId): bool
    {
        $primary = FilterTable::getRow(
            parameters: [
                'select' => ['id'],
                'filter' => [
                    'filter_id' => $filterId
                ]
            ]
        )['id'];

        return FilterTable::update(
            primary: $primary,
            data: [
                'soft_delete' => true
            ]
        )->isSuccess();
    }

    /**
     * @description Get custom filter origin
     *
     * @param string $filterId
     *
     * @return array|null
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getFilterOrigin(string $filterId): ?array
    {
        $customFilter = FilterTable::getRow(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'filter_id' => $filterId
                ]
            ]
        );

        if (empty($customFilter)) {
            return null;
        }

        return FilterHelper::getByOption(
            userId: (int)$customFilter['author_id'],
            optionName: $customFilter['option_name'],
            filterId: $customFilter['filter_id']
        );
    }


}
