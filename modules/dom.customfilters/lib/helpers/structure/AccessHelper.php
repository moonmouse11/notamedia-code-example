<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Structure;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\access\RoleAccessEnum;
use DomDigital\CustomFilters\Enums\ORM\AccessEntityTypeEnum;
use DomDigital\CustomFilters\ORM\Entities\Tables\AccessTable;
use Exception;

final class AccessHelper
{
    /**
     * @description Check if user has access
     *
     * @param array $userData
     *
     * @return bool
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function hasAccess(array $userData): bool
    {
        $userAccess = AccessTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'entity_type' => AccessEntityTypeEnum::USER->value,
                    'entity_id' => $userData['id']
                ]
            ]
        )->fetchAll();

        $departmentAccess = AccessTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value,
                    'entity_id' => $userData['departments']
                ]
            ]
        )->fetchAll();

        return !empty($userAccess) && !empty($departmentAccess);
    }

    /**
     * @description Return list of B24 access to application
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getList(): array
    {
        return AccessTable::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'active' => true
                ]
            ]
        )->fetchAll();
    }

    /**
     * @description Add access to B24 user
     *
     * @param array $userData
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function addAccessToUser(array $userData): bool
    {
        return AccessTable::add(
            data: [
                'role' => RoleAccessEnum::DEFAULT->value,
                'entity_type' => AccessEntityTypeEnum::USER->value,
                'entity_id' => (int) $userData['id'],
                'name' => $userData['full_name'],
                'active' => true
            ]
        )->isSuccess();
    }

    /**
     * @description Add access to B24 department
     *
     * @param array $departmentData
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function addAccessToDepartment(array $departmentData): bool
    {
        return AccessTable::add(
            data: [
                'role' => RoleAccessEnum::DEFAULT->value,
                'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value,
                'entity_id' => (int) $departmentData['id'],
                'name' => $departmentData['title'],
                'active' => true
            ]
        )->isSuccess();
    }

    /**
     * @description Return access record from table
     *
     * @param int $entityId
     * @param AccessEntityTypeEnum $entityType
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function getAccessRow(int $entityId, AccessEntityTypeEnum $entityType): array
    {
        return AccessTable::getRow(
            parameters: [
                'filter' => [
                    'entity_type' => $entityType->value,
                    'entity_id' => $entityId
                ]
            ]
        );
    }

    /**
     * @description Remove access from B24 user
     *
     * @param int $userId
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function removeAccessFromUser(int $userId): bool
    {
        $row = self::getAccessRow(entityId: $userId, entityType: AccessEntityTypeEnum::USER);

        return AccessTable::update(
            primary: $row['id'],
            data: [
                'active' => false
            ]
        )->isSuccess();
    }

    /**
     * @description Remove access from B24 department
     *
     * @param int $departmentId
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function removeAccessFromDepartment(int $departmentId): bool
    {
        $row = self::getAccessRow(entityId: $departmentId, entityType: AccessEntityTypeEnum::DEPARTMENT);

        return AccessTable::update(
            primary: $row['id'],
            data: [
                'active' => false
            ]
        )->isSuccess();
    }
}