<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Option;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Handlers\Options\FilterHandler;

final class FilterHelper
{
    /**
     * @description Returns user filters from CUserOptions with unserialized values
     *
     * @param int $userId
     * @param OptionNameEnum|null $optionNameEnum
     *
     * @return array|null
     *
     * @throws ArgumentException
     */
    public static function getUserFilters(int $userId, OptionNameEnum $optionNameEnum = null): ?array
    {
        $userFilters = [];

        $userOptions = OptionHelper::getUserOptionList(userId: $userId, optionNameEnum: $optionNameEnum);

        foreach ($userOptions as $userOption) {
            $userFilters[] = [
                'type' => OptionNameEnum::getOptionType(optionName: $userOption['name']),
                'filters' => FilterHandler::arrayHandle(filters: $userOption['value']['filters']),
            ];
        }

        return $userFilters;
    }

    /**
     * @description Returns user filter by id and option name
     *
     * @param int $userId
     * @param string $optionName
     * @param string $filterId
     *
     * @return array|null
     *
     * @throws ArgumentException
     */
    public static function getByOption(int $userId, string $optionName, string $filterId): ?array
    {
        $userOptions = OptionHelper::getByOption(
            userId: $userId,
            optionName: $optionName,
        );

        foreach ($userOptions as $userOption) {
            $userFilters[] = [
                'type' => OptionNameEnum::getOptionType(optionName: $userOption['name']),
                'filters' => FilterHandler::arrayHandle(filters: $userOption['value']['filters']),
            ];
        }

        return $userFilters[0]['filters'][$filterId] ?? null;
    }

    /**
     * @description Returns users filters from CUserOptions with unserialized values
     *
     * @param array $users
     * @param OptionNameEnum|null $optionNameEnum
     *
     * @return array|null
     *
     * @throws ArgumentException
     */
    public static function getUsersFilters(array $users, OptionNameEnum $optionNameEnum = null): ?array
    {
        $userFilters = [];

        foreach ($users as $user) {
            $userFilters[$user['id']] = self::getUserFilters(
                userId: $user['id'],
                optionNameEnum: $optionNameEnum
            );
        }

        return $userFilters;
    }

    /**
     * @description Add filter to CUserOptions
     *
     * @param int $userId
     * @param array $filter
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function addFilterToUser(int $userId, array $filter, OptionNameEnum $optionNameEnum, int $dealCategory = null): bool
    {
        return OptionHelper::updateOption(
            userId: $userId,
            filter: $filter,
            nameMask: $optionNameEnum->getOptionName(dealCategory: $dealCategory),
        );
    }

    /**
     * @description Remove filter from CUserOptions
     *
     * @param int $userId
     * @param array $filter
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws ArgumentException
     */
    public static function removeFilterFromUser(int $userId, array $filter, OptionNameEnum $optionNameEnum, int $dealCategory = null): bool
    {
        return OptionHelper::clearOption(
            userId: $userId,
            filterId: $filter['id'],
            nameMask: $optionNameEnum->getOptionName(dealCategory: $dealCategory),
        );
    }

    /**
     * @description Returns filter list
     *
     * @param int|null $userId
     *
     * @return array
     *
     * @throws ArgumentException
     */
    public static function getList(int $userId = null): array
    {
        return array_map(
            callback: static fn($option) => $option['value'],
            array: OptionHelper::getList(userId: $userId)
        );
    }

    /**
     * @description Add filter to users in CUserOptions
     *
     * @param array $users
     * @param array $filter
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function addFilterToUsers(array $users, array $filter, OptionNameEnum $optionNameEnum, int $dealCategory = null): bool
    {
        $result = true;

        foreach ($users as $user) {
            $result = self::addFilterToUser(
                userId: $user['id'],
                filter: $filter,
                optionNameEnum: $optionNameEnum,
                dealCategory: $dealCategory
            );
        }

        return $result;
    }

    /**
     * @description Remove filter from CUserOptions
     *
     * @param array $users
     * @param array $filter
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function removeFilterFromUsers(array $users, array $filter, OptionNameEnum $optionNameEnum, int $dealCategory = null): bool
    {
        $result = true;

        foreach ($users as $user) {
            $result = self::removeFilterFromUser(
                userId: $user['id'],
                filter: $filter,
                optionNameEnum: $optionNameEnum,
                dealCategory: $dealCategory
            );
        }

        return $result;
    }

    /**
     * @description Add new filter to CUserOptions['VALUE']
     *
     * @param array $option
     * @param array $filter
     *
     * @return array
     */
    public static function insertFilterToOption(array $option, array $filter): array
    {
        $option['filters'][$filter['id']] = FilterHandler::prepare($filter);

        return $option;
    }

    /**
     * @description Remove filter from CUserOptions['VALUE']
     *
     * @param array $option
     * @param string $filterId
     *
     * @return array
     */
    public static function unsetFilterFromOption(array $option, string $filterId): array
    {
        unset($option['filters'][$filterId]);

        return $option;
    }


}