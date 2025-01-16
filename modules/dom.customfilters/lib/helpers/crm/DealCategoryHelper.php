<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Crm;

use Bitrix\Crm\Category\DealCategory;
use Bitrix\Main\ArgumentException;

final class DealCategoryHelper
{
    /**
     * @description Returns all deal funnel categories Ids
     *
     * @return array
     *
     * @throws ArgumentException
     */
    public static function getAllDealCategoriesId(): array
    {
        $dealCategories = DealCategory::getAll(enableDefault: true);

        return array_map(
            callback: static fn($item) => (int)$item['ID'],
            array: $dealCategories
        );
    }

    /**
     * @description Check exist B24 deal category
     *
     * @param int $categoryId
     *
     * @return bool
     *
     * @throws ArgumentException
     */
    public static function existDealCategory(int $categoryId): bool
    {
        return in_array(needle: $categoryId, haystack: self::getAllDealCategoriesId(), strict: true);
    }

    /**
     * @description Returns all deal funnel categories map
     *
     * @return array
     *
     * @throws ArgumentException
     */
    public static function getDealCategoriesMap(): array
    {
        return array_map(
            callback: static fn($item) => [
                'id' => (int)$item['ID'],
                'name' => $item['NAME']
            ],
            array: DealCategory::getAll(enableDefault: true)
        );
    }

    /**
     * @description Returns category id from option name
     *
     * @param string $optionName
     *
     * @return int|null
     */
    public static function getCategoryId(string $optionName): ?int
    {
        return str_contains(haystack: $optionName, needle: '_C_')
            ? (int)explode(separator: '_C_', string: $optionName)[1]
            : null;
    }

}