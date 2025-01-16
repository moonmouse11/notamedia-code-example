<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Ajax\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\App\AccessApp;
use Exception;

final class Access extends Controller
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
            'roleList' => [
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
            'remove' => [
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
     * @return array
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function listAction(): array
    {
        return (new AccessApp())->getList();
    }

    public static function roleListAction(): array
    {
        return (new AccessApp())->getRoleList();
    }

    /**
     * @description ajax method for add access to B24 users & departments
     *
     * @param array|null $data
     * @param string $role
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function addAction(array $data = null, string $role = 'default'): array
    {
        return (new AccessApp())->add(
            data: $data,
            role: $role
        );
    }

    /**
     * @description ajax method for update access to B24 users & departments
     *
     * @param array|null $data
     * @param string $role
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function updateAction(array $data = null, string $role = 'default'): array
    {
        return (new AccessApp())->update(
            data: $data,
            role: $role
        );
    }

    /**
     * @description ajax method for remove access from B24 users & departments
     *
     * @param array|null $data
     *
     * @return array
     *
     * @throws Exception
     */
    public static function removeAction(array $data = null): array
    {
        return (new AccessApp())->remove(data: $data);
    }
}