<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Ajax\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\App\FilterApp;

final class Filter extends Controller
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
            'current' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'custom' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'updateCustom' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_POST]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
        ];
    }


    /**
     * @description ajax method for getting list of filters
     *
     * @param array|null $users
     * @param int|null $user_id
     * @return array
     *
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function listAction(array $users = null, int $user_id = null): array
    {
        return (new FilterApp())->getList(
            userId: $user_id,
            users: $users
        );
    }

    /**
     * @description ajax method for getting current user filters
     *
     * @return array
     *
     * @throws ArgumentException
     */
    public static function currentAction(): array
    {
        return (new FilterApp())->getCurrentUserFilters();
    }

    /**
     * @description ajax method for getting list of custom filters
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function customAction(): array
    {
        return (new FilterApp())->getCustomFilters();
    }

    /**
     * @description ajax method for update custom filter
     *
     * @param array $data
     *
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function updateCustomAction(array $data): array
    {
        return (new FilterApp())->updateCustomFilter(data: $data);
    }

}