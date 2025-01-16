<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Ajax\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use DomDigital\CustomFilters\App\FunnelApp;

final class Funnel extends Controller
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
     * @description ajax method for getting list B24 deal funnels
     *
     * @return array
     *
     * @throws ArgumentException
     */
    public static function listAction(): array
    {
        return (new FunnelApp())->getList();
    }
}
