<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Handlers\Options;

final class FilterHandler
{
    /**
     * @description Handle B24 filter from CUserOptions['VALUE']
     *
     * @param string $filterId
     * @param array $filter
     *
     * @return array
     */
    public static function handle(string $filterId, array $filter): array
    {
        return [
            'id' => $filterId,
            'name' => $filter['name'],
            'fields' => $filter['fields'],
            'filter_rows' => $filter['filter_rows'],
        ];
    }

    /**
     * @description Prepare B24 filter for CUserOptions['VALUE']
     *
     * @param array $filter
     *
     * @return array
     */
    public static function prepare(array $filter): array
    {
        return [
            'name' => $filter['name'],
            'fields' => $filter['fields'],
            'filter_rows' => $filter['filter_rows'],
        ];
    }

    /**
     * @description Handle B24 filters from CUserOptions['VALUE'] with defaults
     *
     * @param array $filters
     *
     * @return array
     */
    public static function arrayHandle(array $filters): array
    {
        foreach ($filters as $filterKey => $filter) {
            $filters[$filterKey] = self::handle(filterId: $filterKey, filter: $filter);
        }

        return $filters;
    }

    /**
     * @description Prepare B24 filters for CUserOptions
     *
     * @param array $filters
     *
     * @return array
     */
    public static function arrayPrepare(array $filters): array
    {
        foreach ($filters as $filter) {
            $filters[$filter['id']] = self::prepare(filter: $filter);
        }

        return $filters;
    }
}
