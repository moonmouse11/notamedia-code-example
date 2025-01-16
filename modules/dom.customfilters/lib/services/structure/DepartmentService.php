<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Services\Structure;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Helpers\Option\FilterHelper;
use DomDigital\CustomFilters\Helpers\Structure\DepartmentHelper;

final class DepartmentService
{
    /**
     * @description Include dependencies module
     *
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::includeModule(moduleName: 'iblock');
    }

    public function getList(): array
    {
        return DepartmentHelper::getList();
    }

    /**
     * @description Return list of B24 filters of departments
     *
     * @param int|null $departmentId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getFilterList(int $departmentId = null): array
    {
        if ($departmentId !== null) {
            $users = DepartmentHelper::getUsers(departmentId: $departmentId);

            return FilterHelper::getUsersFilters(users: $users);
        }

        return FilterHelper::getList();
    }

    /**
     * @description Add filter to B24 department
     *
     * @param array $filter
     * @param int $departmentId
     * @param string $filterType
     *
     * @return bool
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function addFilter(array $filter, int $departmentId, string $filterType): bool
    {
        $users = DepartmentHelper::getUsers(departmentId: $departmentId);

        $optionNameEnum = OptionNameEnum::getByType(type: $filterType);

        return FilterHelper::addFilterToUsers(
            users: $users,
            filter: $filter,
            optionNameEnum: $optionNameEnum,
            dealCategory: OptionNameEnum::getDealCategory($filterType)
        );
    }
}