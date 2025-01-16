<?php

namespace DomDigital\WorkDayBlock\Ajax\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\WorkDayBlock\App\UserApp;


final class User extends Controller
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'data' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'status' => [
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
            ],
            'list' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'blockList' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
            'isBlocked' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
        ];
    }


    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException|LoaderException
     */
    public static function dataAction(): array
    {
        return (new UserApp)->getUserData();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function listAction(): array
    {
        return (new UserApp)->getUserList();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws LoaderException
     */
    public static function blockListAction(): array
    {
        return (new UserApp)->getBlockedUserList();
    }

    /**
     * @throws LoaderException
     */
    public static function statusAction(): array
    {
        return (new UserApp)->getUserStatus();
    }

    /**
     * @throws LoaderException
     */
    public static function addAction(array $data): array
    {
        return (new UserApp)->addUserToBlockList(userData: $data);
    }

    /**
     * @throws LoaderException
     */
    public static function removeAction(array $data): array
    {
        return (new UserApp)->removeUserFromBlockList(userData: $data);
    }

    /**
     * @throws LoaderException
     */
    public static function updateAction(array $data): array
    {
        return (new UserApp)->updateUserBlockList(userData: $data);
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isBlockedAction(int $id): array
    {
        return (new UserApp)->isBlocked(userId: $id);
    }

}