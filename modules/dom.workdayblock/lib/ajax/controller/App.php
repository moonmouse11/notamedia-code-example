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
use DomDigital\WorkDayBlock\App\UserApp;

final class App extends Controller
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'save' => [
                'prefilters' => [
                    new HttpMethod(allowedMethods: [HttpMethod::METHOD_POST]),
                    new Scope(scopes: Scope::AJAX)
                ]
            ],
        ];
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function saveAction(array $data = []): array
    {
        $usersList = $data['users'] ?? [];
        $departmentsList = $data['departments'] ?? [];

        return self::saveAppData(usersList: $usersList, departmentsList: $departmentsList);
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function saveAppData(array $usersList, array $departmentsList): array
    {
        $userApp = new UserApp();
        $departmentApp = new DepartmentApp();

        $userApp->cleanUsersBlockList();
        $departmentApp->cleanDepartmentBlockList();

        foreach ($usersList as $userData) {
            $userApp->addOrUpdateUserBlockList(userData: $userData);
        }
        foreach ($departmentsList as $departmentData) {
            $departmentApp->addOrUpdateDepartmentBlockList(departmentData: $departmentData);
        }

        return ['result' => 'maybe'];
    }
}