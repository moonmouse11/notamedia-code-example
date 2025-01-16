<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Services\Option;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Helpers\Option\FilterHelper;

final class FilterService
{
    /**
     * @description Include dependencies module
     *
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::includeModule(moduleName: 'crm');
    }

    /**
     * @description Return list of B24 filters
     *
     * @param int|null $userId
     *
     * @return array
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public function getList(int $userId = null): array
    {
        return FilterHelper::getList(userId: $userId);
    }

    /**
     * @description Return list of B24 user filters
     *
     * @param int $userId
     *
     * @return array|null
     *
     * @throws ArgumentException
     */
    public function getUserFilters(int $userId): ?array
    {
        return FilterHelper::getUserFilters(userId: $userId);
    }

    /**
     * @description Return list of B24 users filters
     *
     * @param array $users
     *
     * @return array|null
     *
     * @throws ArgumentException
     */
    public function getUsersFilters(array $users): ?array
    {
        return FilterHelper::getUsersFilters(users: $users);
    }


}
