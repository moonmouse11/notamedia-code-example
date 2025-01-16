<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Access\RoleAccessEnum;
use DomDigital\CustomFilters\Services\Structure\AccessService;
use Exception;

final class AccessApp
{
    private const EMPTY_DATA = 'empty data';


    /**
     * @description Return list of B24 access.
     *
     * @param bool $active
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getList(bool $active = true): array
    {
        return ['result' => (new AccessService)->getList(active: $active)];
    }

    /**
     * @description Add or update B24 access to application.
     *
     * @param array|null $data
     * @param string $role
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public function add(array $data = null, string $role = 'default'): array
    {
        if ($data !== null) {
            $resultUser = true;
            $resultDepartment = true;

            if (isset($data['users'])) {
                $resultUser = (new AccessService)->addOrUpdateAccessToUsers(
                    users: $data['users'],
                    roleAccessEnum: RoleAccessEnum::getRoleByValue(role: $role)
                );
            }

            if (isset($data['departments'])) {
                $resultDepartment = (new AccessService)->addOrUpdateAccessToDepartments(
                    departments: $data['departments'],
                    roleAccessEnum: RoleAccessEnum::getRoleByValue(role: $role)
                );
            }

            return [
                'result' => [
                    'users' => $resultUser,
                    'departments' => $resultDepartment
                ]
            ];
        }

        return ['result' => self::EMPTY_DATA];
    }

    /**
     * @description Return list of B24 roles.
     *
     * @return array
     */
    public function getRoleList(): array
    {
        return ['result' => RoleAccessEnum::getRoleList()];
    }

    /**
     * @description Update or remove B24 access from application.
     *
     * @param array|null $data
     * @param string $role
     *
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function update(array $data = null, string $role = 'default'): array
    {
        if ($data !== null) {
            $resultUser = true;
            $resultDepartment = true;

            if (isset($data['users'])) {
                $resultUser = (new AccessService)->updateOrRemoveAccessFromUsers(
                    users: $data['users'],
                    roleAccessEnum: RoleAccessEnum::getRoleByValue(role: $role)
                );
            }

            if (isset($data['departments'])) {
                $resultDepartment = (new AccessService)->updateOrRemoveAccessFromDepartments(
                    departments: $data['departments'],
                    roleAccessEnum: RoleAccessEnum::getRoleByValue(role: $role)
                );
            }

            return [
                'result' => [
                    'user' => $resultUser,
                    'department' => $resultDepartment
                ]
            ];
        }

        return ['result' => self::EMPTY_DATA];
    }

    /**
     * @description Remove B24 access from application.
     *
     * @param array|null $data
     *
     * @return array
     *
     * @throws Exception
     */
    public function remove(array $data = null): array
    {
        if ($data !== null) {
            $resultUser = true;
            $resultDepartment = true;

            if (isset($data['users'])) {
                $resultUser = (new AccessService)->removeAccessFromUsers(users: $data['users']);
            }

            if (isset($data['departments'])) {
                $resultDepartment = (new AccessService)->removeAccessFromDepartments(departments: $data['departments']);
            }

            return [
                'result' => [
                    'user' => $resultUser,
                    'department' => $resultDepartment
                ]
            ];
        }

        return ['result' => self::EMPTY_DATA];
    }

}
