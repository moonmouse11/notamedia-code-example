<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\access\RoleAccessEnum;
use DomDigital\CustomFilters\Services\Structure\UserService;

final class UserApp
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
    public function gerUsersList(?array $data = null): array
    {
        return ['result' => (new UserService())->getActiveUsers(data: $data)];
    }

    /**
     * @description Return current user data
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getCurrentUserData(): array
    {
        global $USER;

        return ['result' => (new UserService())->getUserData(userId: (int)$USER->GetID())];
    }

    /**
     * @description Return current user access
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function haveAccess(): array
    {
        global $USER;

        return ['result' => (new UserService())->haveAccess(userId: (int)$USER->GetID())];
    }

}
