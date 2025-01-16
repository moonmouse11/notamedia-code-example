<?php

namespace DomDigital\WorkDayBlock\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CIBlockSection;

final class DepartmentHelper
{
    public function getDepartmentsList(): array
    {
        $result = [];

        $filter = [
            'IBLOCK_ID' => 5,
            'GLOBAL_ACTIVE' => 'Y',
        ];

        $select = [
            '*'
        ];

        $section = CIBlockSection::GetList(
            arOrder: [],
            arFilter: $filter,
            bIncCnt: false,
            arSelect: $select
        );

        while ($department = $section->fetch()) {
            $result[] = $department;
        }

        return array_map(callback: static fn($department) => [
            'id' => $department['ID'],
            'title' => $department['NAME'],
        ], array: $result);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getCurrentUserDepartments(?string $userId): array|false
    {
        return UserTable::getList(
            parameters: [
                'filter' => [
                    'ID' => $userId
                ],
                'select' => ['UF_DEPARTMENT']
            ]
        )->fetch()['UF_DEPARTMENT'];
    }

    public static function getDepartmentData(int $departmentId): ?array
    {
        $filter = [
            'ID' => $departmentId,
            'IBLOCK_ID' => 5,
            'GLOBAL_ACTIVE' => 'Y',
        ];

        $select = [
            '*'
        ];

        return CIBlockSection::GetList(
            arOrder: [],
            arFilter: $filter,
            bIncCnt: false,
            arSelect: $select
        )->fetch();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function inBlockList(int $departmentId, HighLoadBlockHelper $highLoadBlockHelper)
    {
        return $highLoadBlockHelper->isBlockedDepartment(departmentIds: [$departmentId]);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function cleanDepartmentsBlockList(HighLoadBlockHelper $highLoadBlockHelper): bool
    {
        return $highLoadBlockHelper->cleanDepartmentsBlockList();
    }

}
