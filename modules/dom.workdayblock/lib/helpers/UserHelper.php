<?php

namespace DomDigital\WorkDayBlock\Helpers;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;

final class UserHelper
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getActiveUsers(): array
    {
        return array_map(static fn($user) => [
            'id' => $user['ID'],
            'full_name' => self::getFullNameFormat(userData: $user),
        ],
            UserTable::getList(
                parameters: [
                    'filter' => [
                        'ACTIVE' => 'Y',
                        'EXTERNAL_AUTH_ID' => null,
                        'LID' => 's1',
                    ],
                    'select' => ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME']
                ]
            )->fetchAll());
    }

    public static function getFullNameFormat(array $userData): string
    {
        return $userData['LAST_NAME'] . ' ' . $userData['NAME'];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getUserDepartments(?string $userId): array
    {
        return UserTable::getList(
            parameters: [
                'filter' => [
                    'ID' => $userId
                ],
                'select' => ['*']
            ]
        )->fetch()['UF_DEPARTMENT'];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getUserData(int $userId): array
    {
        return UserTable::getList(
            parameters: [
                'filter' => [
                    'ID' => $userId
                ],
                'select' => ['*']
            ]
        )->fetch();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function needBlock(int $userId, HighLoadBlockHelper $highLoadBlockHelper): bool
    {
        $userDepartments = DepartmentHelper::getCurrentUserDepartments(userId: $userId);

        return $highLoadBlockHelper->isBlocked(userId: $userId, departmentIds: $userDepartments);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function inBlockList(int $userId, HighLoadBlockHelper $highLoadBlockHelper): bool
    {
        return $highLoadBlockHelper->isBlockedUser(userId: $userId);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function cleanUsersBlockList(HighLoadBlockHelper $highLoadBlockHelper): bool
    {
        return $highLoadBlockHelper->cleanUsersBlockList();
    }

}
