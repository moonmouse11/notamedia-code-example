<?php

namespace DomDigital\WorkDayBlock\Ajax\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\WorkDayBlock\App\DepartmentApp;

final class Department extends Controller
{
    /**
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
            'blockList' => [
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
            'isBlocked' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
        ];
    }

    /**
     * @return array
     * @throws LoaderException
     */
    public static function listAction(): array
    {
        return (new DepartmentApp)->getDepartmentList();
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function blockListAction(): array
    {
        return (new DepartmentApp)->getDepartmentBlockList();
    }

    /**
     * @throws LoaderException
     */
    public static function addAction(array $data): array
    {
        return (new DepartmentApp)->addDepartmentToBlockList(departmentData: $data);
    }

    /**
     * @throws LoaderException
     */
    public static function removeAction(array $data): array
    {
        return (new DepartmentApp)->removeDepartmentFromBlockList(departmentData: $data);
    }

    /**
     * @throws LoaderException
     */
    public static function updateAction(array $data): array
    {
        return (new DepartmentApp)->updateDepartmentInBlockList(departmentData: $data);
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isBlockedAction(int $id): array
    {
        return (new DepartmentApp)->isBlocked(departmentId: $id);
    }
}