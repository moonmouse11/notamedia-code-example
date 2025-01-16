<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Ajax\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\App\FilterAccessApp;

final class FilterAccess extends Controller
{
    /**
     * @description B24 ajax config method
     *
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'list' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'add' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_POST]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'update' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_POST]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'delete' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_POST]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ]
        ];
    }

    /**
     * @description ajax method for getting list of accesses.
     *
     * @param string|null $filter_id
     * @param string|null $filter_type
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function listAction(string $filter_id = null, string $filter_type = null): array
    {
        return (new FilterAccessApp())->getList(
            filterId: $filter_id,
            filterType: $filter_type
        );
    }

    /**
     * @description ajax method for add access to B24 users & departments
     *
     * @param array|null $data
     *
     * @return array
     * @throws ArgumentNullException
     */
    public static function addAction(?array $data): array
    {
        return (new FilterAccessApp())->add(
            data: $data
        );
    }

    /**
     * @description ajax method for update access to B24 users & departments
     *
     * @param array|null $data
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function updateAction(?array $data): array
    {
        return (new FilterAccessApp())->update(
            data: $data
        );
    }

    /**
     * @description ajax method for deletw access to B24 users & departments
     *
     * @param array|null $data
     *
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function deleteAction(?array $data): array
    {
        return (new FilterAccessApp())->delete(
            data: $data
        );
    }
}