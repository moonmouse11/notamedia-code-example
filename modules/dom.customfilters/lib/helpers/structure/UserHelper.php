<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Helpers\Structure;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use CIntranetUtils;

final class UserHelper
{
    /**
     * @description Return list of B24 active users
     *
     * @param array|null $users
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getActiveUsers(?array $users = null): array
    {
        $filter = [
            'ACTIVE' => 'Y',
            'EXTERNAL_AUTH_ID' => null,
            'LID' => 's1',
        ];

        if ($users !== null) {
            $filter = [
                'ID' => $users,
                'ACTIVE' => 'Y',
                'EXTERNAL_AUTH_ID' => null,
                'LID' => 's1',
            ];
        }

        return array_map(
            callback: static fn($user) => [
                'id' => (int)$user['ID'],
                'full_name' => self::getFullNameFormat(userData: $user),
                'departments' => self::getUserDepartments(userId: (int)$user['ID']),
            ],
            array: UserTable::getList(
                parameters: [
                    'filter' => $filter,
                    'select' => ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME']
                ]
            )->fetchAll()
        );
    }

    /**
     * @description Create full name field for user
     *
     * @param array $userData
     *
     * @return string
     */
    public static function getFullNameFormat(array $userData): string
    {
        return $userData['LAST_NAME'] . ' ' . $userData['NAME'];
    }

    /**
     * @description Return list of B24 user departments
     *
     * @param int $userId
     *
     * @return array|bool
     */
    public static function getUserDepartments(int $userId): array|bool
    {
        return CIntranetUtils::GetUserDepartments(USER_ID: $userId);
    }

    /**
     * @description Return B24 user data
     *
     * @param int $userId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getData(int $userId): array
    {
        $user = UserTable::getRow(
            parameters: [
                'filter' => ['ID' => $userId],
                'select' => [
                    'ID',
                    'NAME',
                    'LAST_NAME',
                    'SECOND_NAME',
                    'UF_DEPARTMENT'
                ]
            ]
        );

        return [
            'id' => (int)$user['ID'],
            'full_name' => self::getFullNameFormat(userData: $user),
            'departments' => self::getUserDepartments(userId: (int)$user['ID']),
        ];
    }

}