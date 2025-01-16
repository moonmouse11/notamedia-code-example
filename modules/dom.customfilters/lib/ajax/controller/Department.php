<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Ajax\Controller;

use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use DomDigital\CustomFilters\App\DepartmentApp;

final class Department extends Controller
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
        ];
    }

    /**
     * @description ajax method for getting list of departments
     *
     * @return array
     */
    public static function listAction(): array
    {
        return (new DepartmentApp())->getList();
    }

}