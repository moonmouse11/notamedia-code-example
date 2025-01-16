<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Handlers\Options;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Helpers\Structure\UserHelper;

final class SwitchAssigmentHandler
{

    /**
     * @description Switch assigment in filter
     *
     * @param array $filter
     * @param int $userId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function handle(array $filter, int $userId): array
    {
        if (self::check(filter: $filter)) {
            $filter = self::switchAssigment(filter: $filter, userId: $userId);
        }

        return $filter;
    }

    /**
     * @description Check if filter has assigment field
     *
     * @param array $filter
     *
     * @return bool
     */
    private static function check(array $filter): bool
    {
        return array_key_exists(key: 'ASSIGNED_BY_ID', array: $filter['fields']);
    }

    /**
     * @description Update filter with new assigment
     *
     * @param array $filter
     * @param int $userId
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function switchAssigment(array $filter, int $userId): array
    {
        $filter['fields']['ASSIGNED_BY_ID'] = $userId;
        $filter['fields']['ASSIGNED_BY_ID_label'] = UserHelper::getData(userId: $userId)['full_name'];

        return $filter;
    }


}