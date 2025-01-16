<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Services\Option\CustomFilterService;
use DomDigital\CustomFilters\Services\Option\FilterService;

final class FilterApp
{
    /**
     * @description Return list of B24 filters for frontend
     *
     * @param int|null $userId
     * @param array|null $users
     *
     * @return array
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public function getList(int $userId = null, array $users = null): array
    {
        if ($users !== null) {
            return ['result' => (new FilterService)->getUsersFilters(users: $users)];
        }

        if ($userId !== null) {
            return ['result' => (new FilterService)->getUserFilters(userId: $userId)];
        }

        global $USER;

        return ['result' => (new FilterService)->getUserFilters(userId: (int)$USER->GetID())];
    }

    /**
     * @description Return current B24 user filters
     *
     * @return array
     *
     * @throws ArgumentException
     */
    public function getCurrentUserFilters(): array
    {
        global $USER;

        return ['result' => (new FilterService)->getUserFilters(userId: (int)$USER->GetID())];
    }

    /**
     * @description Return list of B24 custom filters
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getCustomFilters(): array
    {
        return ['result' => (new CustomFilterService())->getList()];
    }

    /**
     * @description Update custom filter
     *
     * @param array|null $data
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function updateCustomFilter(array $data = null): array
    {
        if ($data !== null) {
            if (array_key_exists(key: 'filter_id', array: $data)) {
                return ['result' => (new CustomFilterService())->updateCustomFilter(filterId: $data['filter_id'])];
            }

            return ['result' => 'no filter id'];
        }

        return ['result' => 'empty data'];
    }

}
