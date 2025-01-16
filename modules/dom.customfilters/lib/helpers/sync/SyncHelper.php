<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Sync;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Enums\ORM\AccessEntityTypeEnum;
use DomDigital\CustomFilters\Helpers\Option\FilterHelper;
use DomDigital\CustomFilters\Helpers\ORM\CustomFilterHelper;
use DomDigital\CustomFilters\Helpers\ORM\FilterAccessHelper;
use DomDigital\CustomFilters\Helpers\Structure\DepartmentHelper;

final class SyncHelper
{

    /**
     * @description Compare and update origin and custom filter
     *
     * @param array $origin
     * @param array $custom
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function compareAndUpdate(array $origin, array $custom): array
    {
        if ($origin !== unserialize(data: $custom['value'], options: ['allowed_classes' => false])) {
            CustomFilterHelper::update(
                filter: $origin,
                optionNameEnum: OptionNameEnum::getByName(name: $custom['option_name']),
                authorId: $custom['author_id'],
                dealCategory: OptionNameEnum::getDealCategoryByName(name: $custom['option_name']),
            );

            $custom = CustomFilterHelper::getById($custom['filter_id']);
        }

        return $custom;
    }

    /**
     * @throws ObjectPropertyException
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function syncFilter(array $customFilter): bool
    {
        $result = true;

        $accesses = FilterAccessHelper::getList($customFilter['filter_id']);

        foreach ($accesses as $access) {
            if ($access['entity_type'] === AccessEntityTypeEnum::USER) {
                $result = FilterHelper::addFilterToUser(
                    userId: $access['entity_id'],
                    filter: $customFilter['value'],
                    optionNameEnum: OptionNameEnum::getByName(name: $customFilter['option_name']),
                    dealCategory: OptionNameEnum::getDealCategoryByName(name: $customFilter['option_name'])
                );
            }
            if ($access['entity_type'] === AccessEntityTypeEnum::DEPARTMENT) {
                $result = FilterHelper::addFilterToUsers(
                    users: DepartmentHelper::getUsers(departmentId: $access['entity_id']),
                    filter: $customFilter['value'],
                    optionNameEnum: OptionNameEnum::getByName(name: $customFilter['option_name']),
                    dealCategory: OptionNameEnum::getDealCategoryByName(name: $customFilter['option_name'])
                );
            }

            if(!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws ArgumentException
     * @throws SystemException
     * @throws \Exception
     */
    public static function deleteFilter(array $customFilter): bool
    {
        $result = true;

        $accesses = FilterAccessHelper::getList($customFilter['filter_id']);

        foreach ($accesses as $access) {
            if ($access['entity_type'] === AccessEntityTypeEnum::USER) {
                $result = FilterHelper::removeFilterFromUser(
                    userId: $access['entity_id'],
                    filter: $customFilter['value'],
                    optionNameEnum: OptionNameEnum::getByName(name: $customFilter['option_name']),
                    dealCategory: OptionNameEnum::getDealCategoryByName(name: $customFilter['option_name'])
                );
            }
            if ($access['entity_type'] === AccessEntityTypeEnum::DEPARTMENT) {
                $result = FilterHelper::removeFilterFromUsers(
                    users: DepartmentHelper::getUsers(departmentId: $access['entity_id']),
                    filter: $customFilter['value'],
                    optionNameEnum: OptionNameEnum::getByName(name: $customFilter['option_name']),
                    dealCategory: OptionNameEnum::getDealCategoryByName(name: $customFilter['option_name'])
                );
            }

            if(!$result) {
                break;
            }
        }

        return CustomFilterHelper::delete(filterId: $customFilter['filter_id']);
    }
}