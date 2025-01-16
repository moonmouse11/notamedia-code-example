<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Option;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CUserOptions;
use DomDigital\CustomFilters\Enums\Option\OptionCategoryEnum;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Handlers\Options\OptionHandler;
use DomDigital\CustomFilters\Handlers\Options\SwitchAssigmentHandler;

final class OptionHelper
{
    /**
     * @description Returns user options from CUserOptions with unserialized values
     *
     * @param int $userId
     * @param OptionNameEnum|null $optionNameEnum
     *
     * @return array|bool
     *
     * @throws ArgumentException
     */
    public static function getUserOptionList(int $userId, OptionNameEnum $optionNameEnum = null): array|bool
    {
        $nameMask = $optionNameEnum !== null ? $optionNameEnum->getOptionName() : OptionNameEnum::ALL_OPTIONS_REGEX;

        $rawUserOptions = CUserOptions::GetList(
            arOrder: ['SORT' => 'ASC'],
            arFilter: [
                'CATEGORY' => OptionCategoryEnum::UI_FILTER->value,
                'NAME_MASK' => $nameMask,
                'USER_ID' => $userId
            ]
        );

        $userOptions = [];

        while ($option = $rawUserOptions->fetch()) {
            $userOptions[] = OptionHandler::handle(option: $option);
        }

        return $userOptions;
    }

    public static function getByOption(int $userId, string $optionName): ?array
    {
        $rawUserOptions = CUserOptions::GetList(
            arOrder: ['SORT' => 'ASC'],
            arFilter: [
                'CATEGORY' => OptionCategoryEnum::UI_FILTER->value,
                'NAME' => $optionName,
                'USER_ID' => $userId
            ]
        );

        $userOptions = [];

        while ($option = $rawUserOptions->fetch()) {
            $userOptions[] = OptionHandler::handle(option: $option);
        }

        return $userOptions;

    }

    /**
     * @description Returns user option unserialized value from CUserOptions
     *
     * @param int $userId
     * @param string $nameMask
     *
     * @return array|bool
     */
    public static function getUserOption(int $userId, string $nameMask): array|bool
    {
        return CUserOptions::GetOption(
            category: OptionCategoryEnum::UI_FILTER->value,
            name: $nameMask,
            default_value: true,
            user_id: $userId
        );
    }

    /**
     * @description Update user option. Add new filter.
     *
     * @param int $userId
     * @param array $filter
     * @param string $nameMask
     *
     * @return bool
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function updateOption(int $userId, array $filter, string $nameMask): bool
    {
        $filter = SwitchAssigmentHandler::handle(filter: $filter, userId: $userId);

        $option = self::getUserOption(userId: $userId, nameMask: $nameMask);

        if(is_bool(value: $option)) {
            $option = self::createDefaultOption(userId: $userId, nameMask: $nameMask);
        }

        $value = FilterHelper::insertFilterToOption(option: $option, filter: $filter);

        return CUserOptions::SetOption(
            category: OptionCategoryEnum::UI_FILTER->value,
            name: $nameMask,
            value: $value,
            bCommon: false,
            user_id: $userId
        );
    }

    /**
     * @description Update user option. Remove filter.
     *
     * @param int $userId
     * @param string $filterId
     * @param string $nameMask
     *
     * @return bool
     */
    public static function clearOption(int $userId, string $filterId, string $nameMask): bool
    {
        $option = self::getUserOption(userId: $userId, nameMask: $nameMask);

        $option = FilterHelper::unsetFilterFromOption(option: $option, filterId: $filterId);

        return CUserOptions::SetOption(
            category: OptionCategoryEnum::UI_FILTER->value,
            name: $nameMask,
            value: $option,
            bCommon: false,
            user_id: $userId
        );
    }

    /**
     * @description Return all 'main.ui.filter' options
     *
     * @param int|null $userId
     * @param OptionNameEnum|null $optionNameEnum
     *
     * @return array|null
     *
     * @throws ArgumentException
     */
    public static function getList(int $userId = null, OptionNameEnum $optionNameEnum = null): ?array
    {
        $nameMask = $optionNameEnum !== null ? $optionNameEnum->getOptionName() : OptionNameEnum::ALL_OPTIONS_REGEX;

        $rawOptions = CUserOptions::GetList(
            arOrder: ['SORT' => 'ASC'],
            arFilter: [
                'CATEGORY' => OptionCategoryEnum::UI_FILTER->value,
                'NAME_MASK' => $nameMask,
                'USER_ID' => $userId
            ]
        );

        $options = [];

        while ($option = $rawOptions->fetch()) {
            $options[$option['USER_ID']] = OptionHandler::handle(option: $option);
        }

        return $options;
    }

    /**
     * @description Create 'main.ui.filter' default option and return it.
     *
     * @param int $userId
     * @param string $nameMask
     *
     * @return array
     */
    private static function createDefaultOption(int $userId, string $nameMask): array
    {
        CUserOptions::SetOption(
            category: OptionCategoryEnum::UI_FILTER->value,
            name: $nameMask,
            value: [],
            bCommon: false,
            user_id: $userId
        );

        return self::getUserOption(userId: $userId, nameMask: $nameMask);
    }
}