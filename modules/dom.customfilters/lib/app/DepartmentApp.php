<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Services\Structure\DepartmentService;

final class DepartmentApp
{
    /**
     * @description Return list of B24 departments
     *
     * @return array
     */
    public function getList(): array
    {
        return ['result' => (new DepartmentService())->getList()];
    }

    /**
     * @description
     *
     * @param int $departmentId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getFilterList(int $departmentId): array
    {
        return ['result' => (new DepartmentService())->getFilterList(departmentId: $departmentId)];
    }

    /**
     * @description Return list of B24 filters
     *
     * @return array
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public function getFiltersList(): array
    {
        return ['result' => (new DepartmentService())->getFilterList()];
    }

    /**
     * @description Add filter to B24 department
     *
     * @param int $departmentId
     * @param array $filter
     * @param string $filterType
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function addFilter(int $departmentId, array $filter, string $filterType): array
    {
        return [
            'result' => (new DepartmentService())->addFilter(
                filter: $filter,
                departmentId: $departmentId,
                filterType: $filterType
            )
        ];
    }

}
