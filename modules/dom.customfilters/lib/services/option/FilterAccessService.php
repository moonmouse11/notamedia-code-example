<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Services\Option;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Enums\ORM\AccessEntityTypeEnum;
use DomDigital\CustomFilters\Helpers\Option\FilterHelper;
use DomDigital\CustomFilters\Helpers\ORM\CustomFilterHelper;
use DomDigital\CustomFilters\Helpers\ORM\FilterAccessHelper;
use DomDigital\CustomFilters\Helpers\Structure\DepartmentHelper;
use Exception;

final class FilterAccessService
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getList(string $filterId = null, string $filterType = null): array
    {
        return FilterAccessHelper::getList(
            filterId: $filterId,
            filterType: $filterType
        );
    }

    /**
     * @throws ArgumentNullException
     * @throws Exception
     */
    private function addToUser(int $userId, array $filter, string $filterType): bool
    {
        FilterHelper::addFilterToUser(
            userId: $userId,
            filter: $filter,
            optionNameEnum: OptionNameEnum::getByType(type: $filterType),
            dealCategory: OptionNameEnum::getDealCategory(type: $filterType)
        );

        return FilterAccessHelper::addOrUpdateFilterToUser(
            userId: $userId,
            filterId: $filter['id'],
            optionNameEnum: OptionNameEnum::getByType(type: $filterType),
            dealCategory: OptionNameEnum::getDealCategory(type: $filterType)
        );
    }

    /**
     * @throws ArgumentNullException
     * @throws Exception
     */
    private function addToDepartment(int $departmentId,array $filter, string $filterType): bool
    {
        FilterHelper::addFilterToUsers(
            users: DepartmentHelper::getUsers(departmentId: $departmentId),
            filter: $filter,
            optionNameEnum: OptionNameEnum::getByType(type: $filterType),
            dealCategory: OptionNameEnum::getDealCategory(type: $filterType)
        );

        return FilterAccessHelper::addOrUpdateFilterToDepartment(
            departmentId: $departmentId,
            filterId: $filter['id'],
            optionNameEnum: OptionNameEnum::getByType(type: $filterType),
            dealCategory: OptionNameEnum::getDealCategory(type: $filterType)
        );
    }

    /**
     * @throws ArgumentNullException
     * @throws Exception
     */
    public function addToUsers(array $users, array $filter, string $filterType, int $authorId): bool
    {
        CustomFilterHelper::createOrUpdate(
            filter: $filter,
            optionNameEnum: OptionNameEnum::getByType(type: $filterType),
            authorId: $authorId,
            dealCategory: OptionNameEnum::getDealCategory(type: $filterType),
            common: false
        );

        $result = true;

        foreach ($users as $user) {
            $result = $this->addToUser(userId: $user, filter: $filter, filterType: $filterType);

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @throws ArgumentNullException
     * @throws Exception
     */
    public function addToDepartments(array $departments,array $filter, string $filterType, int $authorId): bool
    {
        CustomFilterHelper::createOrUpdate(
            filter: $filter,
            optionNameEnum: OptionNameEnum::getByType(type: $filterType),
            authorId: $authorId,
            dealCategory: OptionNameEnum::getDealCategory(type: $filterType),
            common: false
        );


        $result = true;

        foreach ($departments as $department) {
            $result = $this->addToDepartment(departmentId: $department, filter: $filter, filterType: $filterType);

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function updateToUsers(array $users, array $filter, string $filterType): bool
    {
        $result = true;

        $accessList = FilterAccessHelper::getList(filterId: $filter['id'], filterType: $filterType);

        foreach ($accessList as $access) {
            if($access['entity_type'] === AccessEntityTypeEnum::USER){
                if (!in_array(needle: $access['entity_id'], haystack: $users, strict: false)) {
                    $result = $this->removeFromUser(userId: $access['entity_id'], filter: $filter);
                } else {
                    unset($users, $access['entity_id']);
                }
            }
        }

        if(!empty($users)) {
            $result = $this->addToUsers(users: $users, filter: $filter, filterType: $filterType);
        }

        return $result;
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function updateToDepartments(array $departments, array $filter, string $filterType): bool
    {
        $result = true;

        $accessList = FilterAccessHelper::getList(filterId: $filter['id'], filterType: $filterType);

        foreach ($accessList as $access) {
            if($access['entity_type'] === AccessEntityTypeEnum::DEPARTMENT){
                if (!in_array(needle: $access['entity_id'], haystack: $departments, strict: false)) {
                    $result = $this->removeFromDepartment(departmentId: $access['entity_id'], filter: $filter);
                } else {
                    unset($departments, $access['entity_id']);
                }
            }
        }

        if(!empty($departments)) {
            $result = $this->addToDepartments(departments: $departments, filter: $filter, filterType: $filterType);
        }

        return $result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private function removeFromUser(int $userId, array $filter): bool
    {
        FilterHelper::removeFilterFromUser(
            userId: $userId,
            filter: $filter,
            optionNameEnum: OptionNameEnum::getByType(type: $filter['type']),
            dealCategory: OptionNameEnum::getDealCategory(type: $filter['type'])
        );

        return FilterAccessHelper::deleteFilterFromUser(
            filterId: $filter['id'],
            userId: $userId,
        );
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private function removeFromDepartment(int $departmentId, array $filter): bool
    {
        $users = DepartmentHelper::getUsers(departmentId: $departmentId);

        foreach ($users as $user) {
            if (!FilterAccessHelper::isExistsUser(userId: $user['id'], filterId: $filter['id'])) {
                FilterHelper::removeFilterFromUser(
                    userId: $user['id'],
                    filter: $filter,
                    optionNameEnum: OptionNameEnum::getByType(type: $filter['type']),
                    dealCategory: OptionNameEnum::getDealCategory(type: $filter['type'])
                );
            }
        }

        return FilterAccessHelper::deleteFilterFromDepartment(
            filterId: $filter['id'],
            departmentId: $departmentId,
        );
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function removeFromUsers(array $users, array $filter): bool
    {
        $result = true;

        foreach ($users as $user) {
            $result = $this->removeFromUser(userId: $user, filter: $filter);

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function removeFromDepartments(array $departments, array $filter): bool
    {
        $result = true;

        foreach ($departments as $department) {
            $result = $this->removeFromDepartment(departmentId: $department, filter: $filter);

            if (!$result) {
                break;
            }
        }

        return $result;
    }
}