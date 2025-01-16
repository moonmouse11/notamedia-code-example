<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Services\Structure;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Helpers\ORM\AccessHelper;
use DomDigital\CustomFilters\Helpers\Structure\UserHelper;

final class UserService
{
    /**
     * @description Return list of B24 users
     *
     * @param array|null $data
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getActiveUsers(?array $data = null): array
    {
        if ($data !== null) {
            return UserHelper::getActiveUsers(users: $data['users']);
        }
        return UserHelper::getActiveUsers();
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
    public function getUserData(int $userId): array
    {
        return UserHelper::getData(userId: $userId);
    }

    /**
     * @description Check if B24 user has access
     *
     * @param int $userId
     *
     * @return string
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function haveAccess(int $userId): string
    {
        $userData = UserHelper::getData(userId: $userId);

        return AccessHelper::haveAccess(userData: $userData);
    }
}
