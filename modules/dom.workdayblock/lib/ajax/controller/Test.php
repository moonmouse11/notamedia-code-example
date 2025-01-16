<?php

namespace DomDigital\WorkDayBlock\Ajax\Controller;

use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Bitrix\Main\Engine\Controller;

class Test extends Controller
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'example' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_GET]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public static function exampleAction(): array
    {
        return [
            'test' => 'Example',
        ];
    }
}