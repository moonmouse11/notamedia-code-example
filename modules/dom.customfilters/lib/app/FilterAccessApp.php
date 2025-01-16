<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Services\Option\FilterAccessService;

final class FilterAccessApp
{
    private const EMPTY_DATA = 'empty data';

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getList(?string $filterId = null, ?string $filterType = null): array
    {
        return [
            'result' => (new FilterAccessService())->getList(
                filterId: $filterId,
                filterType: $filterType
            )
        ];
    }

    /**
     * @throws ArgumentNullException
     */
    public function add(array $data = null): array
    {
        if ($data !== null) {
            $result = true;

            if ($data['users'] !== null) {
                $result = (new FilterAccessService)->addToUsers(
                    users: $data['users'],
                    filter: $data['filter'],
                    filterType: $data['filterType'],
                    authorId: $data['authorId']
                );
            }

            if ($data['departments'] !== null) {
                $result = (new FilterAccessService)->addToDepartments(
                    departments: $data['departments'],
                    filter: $data['filter'],
                    filterType: $data['filterType'],
                    authorId: $data['authorId']
                );
            }

            return ['result' => $result];
        }

        return ['result' => self::EMPTY_DATA];
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function update(array $data = null): array
    {
        if ($data !== null) {
            $result = true;

            if ($data['users'] !== null) {
                $result = (new FilterAccessService)->updateToUsers(
                    users: $data['users'],
                    filter: $data['filter'],
                    filterType: $data['filterType'],

                );
            }

            if ($data['departments'] !== null) {
                $result = (new FilterAccessService)->updateToDepartments(
                    departments: $data['departments'],
                    filter: $data['filter'],
                    filterType: $data['filterType']
                );
            }

            return ['result' => $result];
        }

        return ['result' => self::EMPTY_DATA];
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function delete(array $data = null): array
    {
        if ($data !== null) {
            $result = true;

            if ($data['users'] !== null) {
                $result = (new FilterAccessService)->removeFromUsers(
                    users: $data['users'],
                    filter: $data['filter'],
                );
            }

            if ($data['departments'] !== null) {
                $result = (new FilterAccessService)->removeFromDepartments(
                    departments: $data['departments'],
                    filter: $data['filter'],
                );
            }

            return ['result' => $result];
        }

        return ['result' => self::EMPTY_DATA];
    }

}
