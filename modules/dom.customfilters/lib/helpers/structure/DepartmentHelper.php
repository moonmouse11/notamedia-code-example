<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Structure;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CIBlockSection;

final class DepartmentHelper
{
    private const DEPARTMENT_IBLOCK_ID = 5;

    /**
     * @description Return list of B24 departments
     *
     * @return array
     */
    public static function getList(): array
    {
        $result = [];

        $filter = [
            'IBLOCK_ID' => self::DEPARTMENT_IBLOCK_ID,
            'GLOBAL_ACTIVE' => 'Y',
        ];

        $section = CIBlockSection::GetList(
            arOrder: [],
            arFilter: $filter,
            bIncCnt: false,
            arSelect: ['ID', 'NAME']
        );

        while ($department = $section->fetch()) {
            $result[] = $department;
        }

        return array_map(
            callback: static fn($department) => [
                'id' => (int) $department['ID'],
                'title' => $department['NAME'],
            ],
            array: $result
        );
    }

    /**
     * @description Return B24 department data
     *
     * @param int $departmentId
     *
     * @return array|null
     */
    public static function getData(int $departmentId): ?array
    {
        $filter = [
            'ID' => $departmentId,
            'IBLOCK_ID' => self::DEPARTMENT_IBLOCK_ID,
            'GLOBAL_ACTIVE' => 'Y',
        ];

        $departmentData = CIBlockSection::GetList(
            arOrder: [],
            arFilter: $filter,
            bIncCnt: false,
            arSelect: ['ID', 'NAME']
        )->fetch();

        return [
            'id' => (int) $departmentData['ID'],
            'title' => $departmentData['NAME'],
        ];
    }

    /**
     * @description Return list of B24 users in department
     *
     * @param int $departmentId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getUsers(int $departmentId): array
    {
        return array_map(
            callback: static fn($user) => [
                'id' => (int) $user['ID'],
                'full_name' => $user['LAST_NAME'] . ' ' . $user['NAME'],
                'departments' => $user['UF_DEPARTMENT'],
            ],
            array: UserTable::GetList(
                parameters: [
                    'select' => ['ID', 'NAME', 'LAST_NAME', 'UF_DEPARTMENT'],
                    'filter' => [
                        'ACTIVE' => 'Y',
                        'EXTERNAL_AUTH_ID' => null,
                        'LID' => 's1',
                        'UF_DEPARTMENT' => $departmentId
                    ]
                ]
            )->fetchAll()
        );
    }
}