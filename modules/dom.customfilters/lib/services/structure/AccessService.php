<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Services\Structure;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\access\RoleAccessEnum;
use DomDigital\CustomFilters\Enums\ORM\AccessEntityTypeEnum;
use DomDigital\CustomFilters\Helpers\ORM\AccessHelper;
use DomDigital\CustomFilters\Helpers\Structure\DepartmentHelper;
use DomDigital\CustomFilters\Helpers\Structure\UserHelper;
use Exception;

final class AccessService
{
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
        return AccessHelper::getList(active: $active);
    }

    /**
     * @description Add access to B24 user
     *
     * @param int $userId
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    private function addOrUpdateAccessToUser(int $userId, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $userData = UserHelper::getData(userId: $userId);

        return AccessHelper::addOrUpdateAccessToUser(userData: $userData, roleAccessEnum: $roleAccessEnum);
    }

    /**
     * @description Add access to B24 users
     *
     * @param array $users
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function addOrUpdateAccessToUsers(array $users, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $result = true;

        foreach ($users as $user) {
            $result = $this->addOrUpdateAccessToUser(userId: $user['id'], roleAccessEnum: $roleAccessEnum);

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @description Add access to B24 department
     *
     * @param int $departmentId
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws Exception
     */
    private function addOrUpdateAccessToDepartment(int $departmentId, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $departmentData = DepartmentHelper::getData(departmentId: $departmentId);

        return AccessHelper::addOrUpdateAccessToDepartment(departmentData: $departmentData, roleAccessEnum: $roleAccessEnum);
    }

    /**
     * @description Add access to B24 departments
     *
     * @param array $departments
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws Exception
     */
    public function addOrUpdateAccessToDepartments(array $departments, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $result = true;

        foreach ($departments as $department) {
            $result = $this->addOrUpdateAccessToDepartment(departmentId: $department['id'], roleAccessEnum: $roleAccessEnum);

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @description Update or remove access from B24 users
     *
     * @param array $users
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function updateOrRemoveAccessFromUsers(array $users, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $result = true;

        $accessList = AccessHelper::getList();

        foreach ($accessList as $access) {
            if($access['entity_type'] === AccessEntityTypeEnum::USER->value) {
                if(!in_array(needle: $access['entity_id'], haystack:  $users, strict: true)) {
                    $result = $this->removeAccessFromUser(userId: $access['entity_id']);
                } else {
                    $result = $this->addOrUpdateAccessToUser(userId: $access['entity_id'], roleAccessEnum: $roleAccessEnum);
                    unset($users[$access['entity_id']]);
                }
            }

            if(!$result) {
                break;
            }
        }

        if(!empty($users)) {
            $result = $this->addOrUpdateAccessToUsers(users: $users, roleAccessEnum: $roleAccessEnum);
        }

        return $result;
    }

    /**
     * @description Update or remove access from B24 departments
     *
     * @param array $departments
     * @param RoleAccessEnum $roleAccessEnum
     *
     * @return bool
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public function updateOrRemoveAccessFromDepartments(array $departments, RoleAccessEnum $roleAccessEnum = RoleAccessEnum::DEFAULT): bool
    {
        $result = true;

        $accessList = AccessHelper::getList();

        foreach ($accessList as $access) {
            if($access['entity_type'] === AccessEntityTypeEnum::DEPARTMENT->value) {
                if(!in_array(needle: $access['entity_id'], haystack:  $departments, strict: true)) {
                    $result = $this->removeAccessFromDepartment(departmentId: $access['entity_id']);
                } else {
                    $result = $this->addOrUpdateAccessToDepartment(departmentId: $access['entity_id'], roleAccessEnum: $roleAccessEnum);
                    unset($departments[$access['entity_id']]);
                }
            }

            if(!$result) {
                break;
            }
        }

        if(!empty($departments)) {
            $result = $this->addOrUpdateAccessToDepartments(departments: $departments, roleAccessEnum: $roleAccessEnum);
        }

        return $result;
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
    private function removeAccessFromUser(int $userId): bool
    {
        return AccessHelper::removeAccessFromUser(userId: $userId);
    }

    /**
     * @description Remove access from B24 users
     *
     * @param array $users
     *
     * @return bool
     *
     * @throws Exception
     */
    public function removeAccessFromUsers(array $users): bool
    {
        $result = true;

        foreach ($users as $user) {
            $result = $this->removeAccessFromUser(userId: $user['id']);

            if (!$result) {
                break;
            }
        }

        return $result;
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
    private function removeAccessFromDepartment(int $departmentId): bool
    {
        return AccessHelper::removeAccessFromDepartment(departmentId: $departmentId);
    }

    /**
     * @description Remove access from B24 departments
     *
     * @param array $departments
     *
     * @return bool
     *
     * @throws Exception
     */
    public function removeAccessFromDepartments(array $departments): bool
    {
        $result = true;

        foreach ($departments as $department) {
            $result = $this->removeAccessFromDepartment(departmentId: $department['id']);

            if (!$result) {
                break;
            }
        }

        return $result;
    }


}