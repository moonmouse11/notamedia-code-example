<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Ajax\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\App\UserFieldApp;

final class UserField extends Controller
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
            ]
        ];
    }

    /**
     * @description ajax method for getting list B24 userfields data
     *
     * @param string $entity_type
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function listAction(string $entity_type = ''): array
    {
        return (new UserFieldApp())->getList(entityType: $entity_type);
    }
}