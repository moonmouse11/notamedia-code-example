<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\ORM;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Enums\ORM\AccessEntityTypeEnum;
use DomDigital\CustomFilters\ORM\Entities\Tables\FilterRelationTable;
use Exception;

final class FilterAccessHelper
{
    /**
     * @description Return list of custom filters
     *
     * @param string|null $filterId
     * @param string|null $filterType
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getList(string $filterId = null, string $filterType = null): array
    {
        if ($filterId !== null) {
            return FilterRelationTable::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        'filter_id' => $filterId
                    ]
                ]
            )->fetchAll();
        }

        if ($filterType !== null) {
            return FilterRelationTable::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        'entity_type' => $filterType,
                    ]
                ]
            )->fetchAll();
        }

        return FilterRelationTable::getList(
            parameters: [
                'select' => ['*'],
            ]
        )->fetchAll();
    }

    /**
     * @description Return list of custom filter by id
     *
     * @param string $filterId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getListByFilterId(string $filterId): array
    {
        return FilterRelationTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'filter_id' => $filterId
                ]
            ]
        )->fetchAll();
    }

    /**
     * @description Return list of custom filter by department
     *
     * @param int $departmentId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getListByDepartmentId(int $departmentId): array
    {
        return FilterRelationTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'entity_id' => $departmentId,
                    'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value
                ]
            ]
        )->fetchAll();
    }

    /**
     * @description Return list of custom filter by user
     *
     * @param int $userId
     * @param array $departmentIds
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getListByUserId(int $userId, array $departmentIds): array
    {
        $userFilters = FilterRelationTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'entity_id' => $userId,
                    'entity_type' => AccessEntityTypeEnum::USER->value
                ]
            ]
        )->fetchAll();

        $departmentFilters = [];

        foreach ($departmentIds as $departmentId) {
            $departmentFilters['department_' . $departmentId] = self::getListByDepartmentId(
                departmentId: $departmentId
            );
        }

        return array_merge($userFilters, $departmentFilters);
    }

    /**
     * @description Add custom filter to B24 department
     *
     * @param int $departmentId
     * @param string $filterId
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function addFilterToDepartment(
        int $departmentId,
        string $filterId,
        OptionNameEnum $optionNameEnum,
        int $dealCategory = null
    ): bool {
        return FilterRelationTable::add(
            data: [
                'filter_id' => $filterId,
                'filter_type' => $optionNameEnum->getOptionName(dealCategory: $dealCategory),
                'entity_id' => $departmentId,
                'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value
            ]
        )->isSuccess();
    }

    /**
     * @description Update custom filter to B24 department
     *
     * @param int $departmentId
     * @param string $filterId
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function updateFilterToDepartment(
        int $departmentId,
        string $filterId,
        OptionNameEnum $optionNameEnum,
        int $dealCategory = null
    ): bool {
        $primaryKey = self::getRowDepartment(departmentId: $departmentId, filterId: $filterId)['id'];

        return FilterRelationTable::update(
            primary: $primaryKey,
            data: [
                'filter_id' => $filterId,
                'filter_type' => $optionNameEnum->getOptionName(dealCategory: $dealCategory),
                'entity_id' => $departmentId,
                'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value
            ]
        )->isSuccess();
    }

    /**
     * @description Add custom filter to B24 user
     *
     * @param int $userId
     * @param string $filterId
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function addFilterToUser(
        int $userId,
        string $filterId,
        OptionNameEnum $optionNameEnum,
        int $dealCategory = null
    ): bool {
        return FilterRelationTable::add(
            data: [
                'filter_id' => $filterId,
                'filter_type' => $optionNameEnum->getOptionName(dealCategory: $dealCategory),
                'entity_id' => $userId,
                'entity_type' => AccessEntityTypeEnum::USER->value
            ]
        )->isSuccess();
    }

    /**
     * @description Update custom filter to B24 user
     *
     * @param int $userId
     * @param string $filterId
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function updateFilterToUser(
        int $userId,
        string $filterId,
        OptionNameEnum $optionNameEnum,
        int $dealCategory = null
    ): bool {
        $primaryKey = self::getRowUser(userId: $userId, filterId: $filterId)['id'];

        return FilterRelationTable::update(
            primary: $primaryKey,
            data: [
                'filter_id' => $filterId,
                'filter_type' => $optionNameEnum->getOptionName(dealCategory: $dealCategory),
                'entity_id' => $userId,
                'entity_type' => AccessEntityTypeEnum::USER->value
            ]
        )->isSuccess();
    }

    /**
     * @description Add or update filter to B24 department
     *
     * @param int $departmentId
     * @param string $filterId
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function addOrUpdateFilterToDepartment(
        int $departmentId,
        string $filterId,
        OptionNameEnum $optionNameEnum,
        int $dealCategory = null
    ): bool {
        if (self::isExistsDepartment(departmentId: $departmentId, filterId: $filterId)) {
            return self::updateFilterToDepartment(
                departmentId: $departmentId,
                filterId: $filterId,
                optionNameEnum: $optionNameEnum,
                dealCategory: $dealCategory
            );
        }

        return self::addFilterToDepartment(
            departmentId: $departmentId,
            filterId: $filterId,
            optionNameEnum: $optionNameEnum,
            dealCategory: $dealCategory
        );
    }

    /**
     * @description Add or update filter to B24 user
     *
     * @param int $userId
     * @param string $filterId
     * @param OptionNameEnum $optionNameEnum
     * @param int|null $dealCategory
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function addOrUpdateFilterToUser(
        int $userId,
        string $filterId,
        OptionNameEnum $optionNameEnum,
        int $dealCategory = null
    ): bool {
        if (self::isExistsUser(userId: $userId, filterId: $filterId)) {
            return self::updateFilterToUser(
                userId: $userId,
                filterId: $filterId,
                optionNameEnum: $optionNameEnum,
                dealCategory: $dealCategory
            );
        }

        return self::addFilterToUser(
            userId: $userId,
            filterId: $filterId,
            optionNameEnum: $optionNameEnum,
            dealCategory: $dealCategory
        );
    }

    /**
     * @description Get B24 department record from table by filter id
     *
     * @param int $departmentId
     * @param string $filterId
     *
     * @return array|null
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function getRowDepartment(int $departmentId, string $filterId): array|null
    {
        return FilterRelationTable::getRow(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'entity_id' => $departmentId,
                    'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value,
                    'filter_id' => $filterId
                ]
            ]
        );
    }

    /**
     * @description Get B24 user record from table by filter id
     *
     * @param int $userId
     * @param string $filterId
     *
     * @return array|null
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function getRowUser(int $userId, string $filterId): array|null
    {
        return FilterRelationTable::getRow(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'entity_id' => $userId,
                    'entity_type' => AccessEntityTypeEnum::USER->value,
                    'filter_id' => $filterId
                ]
            ]
        );
    }

    /**
     * @description Check if B24 department record exists
     *
     * @param int $departmentId
     * @param string $filterId
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isExistsDepartment(int $departmentId, string $filterId): bool
    {
        return self::getRowDepartment(departmentId: $departmentId, filterId: $filterId) !== null;
    }

    /**
     * @description Check if B24 user record exists
     *
     * @param int $userId
     * @param string $filterId
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isExistsUser(int $userId, string $filterId): bool
    {
        return self::getRowUser(userId: $userId, filterId: $filterId) !== null;
    }

    /**
     * @description Delete filter record from B24 department
     *
     * @param string $filterId
     * @param int $departmentId
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function deleteFilterFromDepartment(string $filterId, int $departmentId): bool
    {
        $primaryKey = FilterRelationTable::getRow(
            parameters: [
                'select' => ['id'],
                'filter' => [
                    'filter_id' => $filterId,
                    'entity_id' => $departmentId,
                    'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value
                ]
            ]
        )['id'];

        return FilterRelationTable::delete(primary: $primaryKey)->isSuccess();
    }

    /**
     * @description Delete filter record from B24 user
     *
     * @param string $filterId
     * @param int $userId
     *
     * @return bool
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public static function deleteFilterFromUser(string $filterId, int $userId): bool
    {
        if (self::isExistsUser(userId: $userId, filterId: $filterId)) {
            $primaryKey = FilterRelationTable::getRow(
                parameters: [
                    'select' => ['id'],
                    'filter' => [
                        'filter_id' => $filterId,
                        'entity_id' => $userId,
                        'entity_type' => AccessEntityTypeEnum::USER->value
                    ]
                ]
            )['id'];

            return FilterRelationTable::delete(primary: $primaryKey)->isSuccess();
        }

        return true;
    }

}