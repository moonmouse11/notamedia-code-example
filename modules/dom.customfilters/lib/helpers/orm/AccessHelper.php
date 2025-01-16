<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\ORM;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Access\RoleAccessEnum;
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
     * @return string
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function haveAccess(array $userData): string
    {
        global $USER;

        if ($USER->IsAdmin()) {
            return RoleAccessEnum::ADMIN->value;
        }

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

        return RoleAccessEnum::getRole($userAccess, $departmentAccess);
    }

    /**
     * @description Return list of B24 access to application
     *
     * @param bool $active
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getList(bool $active = true): array
    {
        return array_map(
            callback: static function ($item) {
                if ($item['entity_type'] === AccessEntityTypeEnum::USER->value) {
                    $item['users'][] = [
                        'id' => $item['entity_id'],
                        'full_name' => $item['name'],
                        'role' => $item['role']
                    ];
                }

                if ($item['entity_type'] === AccessEntityTypeEnum::DEPARTMENT->value) {
                    $item['departments'][] = [
                        'id' => $item['entity_id'],
                        'title' => $item['name'],
                        'role' => $item['role']
                    ];
                }
            },
            array: AccessTable::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        'active' => $active
                    ]
                ]
            )->fetchAll()
        );
    }

    /**
     * @description Return list of B24 users access to application
     *
     * @param bool $active
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getUserAccessList(bool $active = true): array
    {
        return array_map(
            callback: static function ($item) {
                $item = [
                    'id' => $item['entity_id'],
                    'full_name' => $item['name'],
                    'role' => $item['role']
                ];
            },
            array: AccessTable::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        'entity_type' => AccessEntityTypeEnum::USER->value,
                        'active' => $active
                    ]
                ]
            )->fetchAll()
        );
    }


    /**
     * @description Return list of B24 departments access to application
     *
     * @param bool $active
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getDepartmentAccessList(bool $active = true): array
    {
        return array_map(
            callback: static function ($item) {
                $item = [
                    'id' => $item['entity_id'],
                    'title' => $item['name'],
                    'role' => $item['role']
                ];
            },
            array: AccessTable::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value,
                        'active' => $active
                    ]
                ]
            )->fetchAll()
        );
    }

    /**
     * @description Add access to B24 user
     *
     * @param array $userData
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function addAccessToUser(array $userData, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool {
        return AccessTable::add(
            data: [
                'role' => $roleAccessEnum->value,
                'entity_type' => AccessEntityTypeEnum::USER->value,
                'entity_id' => (int)$userData['id'],
                'name' => $userData['full_name'],
                'active' => true
            ]
        )->isSuccess();
    }

    /**
     * @description Add or restore access to B24 user
     *
     * @param array $userData
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public static function addOrUpdateAccessToUser(array $userData, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        if (self::accessExists(userData: $userData)) {
            return self::updateAccessToUser(
                userData: $userData,
                roleAccessEnum: $roleAccessEnum
            );
        }

        return self::addAccessToUser(
            userData: $userData,
            roleAccessEnum: $roleAccessEnum
        );
    }

    /**
     * @description Add access to B24 department
     *
     * @param array $departmentData
     * @param RoleAccessEnum $roleAccessEnum
     * @return bool
     *
     * @throws Exception
     */
    public static function addOrUpdateAccessToDepartment(array $departmentData, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        if(self::departmentAccessExists(departmentData: $departmentData)) {
            return self::updateAccessToDepartment(
                departmentData: $departmentData,
                roleAccessEnum: $roleAccessEnum
            );
        }

        return AccessTable::add(
            data: [
                'role' => $roleAccessEnum->value,
                'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value,
                'entity_id' => (int)$departmentData['id'],
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
     * @return array|null
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function getAccessRow(int $entityId, AccessEntityTypeEnum $entityType): array|null
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
        if(self::accessExists(userData: ['id' => $userId])) {
            $row = self::getAccessRow(entityId: $userId, entityType: AccessEntityTypeEnum::USER);

            return AccessTable::update(
                primary: $row['id'],
                data: [
                    'active' => false
                ]
            )->isSuccess();
        }

        return true;
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
        if(self::departmentAccessExists(departmentData: ['id' => $departmentId])) {
            $row = self::getAccessRow(entityId: $departmentId, entityType: AccessEntityTypeEnum::DEPARTMENT);

            return AccessTable::update(
                primary: $row['id'],
                data: [
                    'active' => false
                ]
            )->isSuccess();
        }

        return true;
    }

    /**
     * @description Check if access exists (with non-active status)
     *
     * @param array $userData
     *
     * @return bool
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function accessExists(array $userData): bool
    {
        return !empty(self::getAccessRow(entityId: $userData['id'], entityType: AccessEntityTypeEnum::USER));
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function departmentAccessExists(array $departmentData): bool
    {
        var_dump($departmentData);

        return !empty(self::getAccessRow(entityId: $departmentData['id'], entityType: AccessEntityTypeEnum::DEPARTMENT));
    }

    /**
     * @description Restore access to B24 user
     *
     * @param array $userData
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function updateAccessToUser(array $userData, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $row = self::getAccessRow(entityId: $userData['id'], entityType: AccessEntityTypeEnum::USER);

        return AccessTable::update(
            primary: $row['id'],
            data: [
                'role' => $roleAccessEnum->value,
                'entity_type' => AccessEntityTypeEnum::USER->value,
                'entity_id' => (int)$userData['id'],
                'name' => $userData['full_name'],
                'active' => true
            ]
        )->isSuccess();
    }

    /**
     * @description Restore access to B24 user
     *
     * @param array $departmentData
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    private static function updateAccessToDepartment(array $departmentData, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $row = self::getAccessRow(entityId: $departmentData['id'], entityType: AccessEntityTypeEnum::DEPARTMENT);

        return AccessTable::update(
            primary: $row['id'],
            data: [
                'role' => $roleAccessEnum->value,
                'entity_type' => AccessEntityTypeEnum::DEPARTMENT->value,
                'entity_id' => (int)$departmentData['id'],
                'name' => $departmentData['title'],
                'active' => true
            ]
        )->isSuccess();
    }
}