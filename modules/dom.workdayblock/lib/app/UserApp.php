<?php

namespace DomDigital\WorkDayBlock\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\WorkDayBlock\Helpers\DepartmentHelper;
use DomDigital\WorkDayBlock\Helpers\HighLoadBlockHelper;
use DomDigital\WorkDayBlock\Helpers\TimeManHelper;
use DomDigital\WorkDayBlock\Helpers\UserHelper;
use Exception;

final class UserApp
{
    private UserHelper $userHelper;
    private HighLoadBlockHelper $highLoadBlockHelper;
    private TimeManHelper $timeManHelper;
    private DepartmentHelper $departmentHelper;

    public function __construct()
    {
        $this->userHelper = new UserHelper();
        $this->departmentHelper = new DepartmentHelper();
        $this->highLoadBlockHelper = new HighLoadBlockHelper();
        $this->timeManHelper = new TimeManHelper();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws LoaderException
     */
    public function getBlockedUserList(): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return $this->highLoadBlockHelper->getBlockedUsers();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws LoaderException
     */
    public function getUserData(): array
    {
        global $USER;

        Loader::includeModule(moduleName: 'highloadblock');
        Loader::IncludeModule(moduleName: 'timeman');

        return [
            'id' => $USER->GetID(),
            'full_name' => $USER->GetFormattedName(),
            'departments' => $this->departmentHelper::getCurrentUserDepartments(userId: $USER->GetID()),
            'status' => $this->timeManHelper->getUserStatus(userId: $USER->GetID()),
            'need_block' => $this->userHelper->needBlock(
                userId: $USER->GetID(),
                highLoadBlockHelper: $this->highLoadBlockHelper
            ),
        ];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getUserList(): array
    {
        return $this->userHelper->getActiveUsers();
    }

    /**
     * @throws LoaderException
     */
    public function getUserStatus(): array
    {
        global $USER;

        Loader::IncludeModule(moduleName: 'timeman');

        return [
            'id' => $USER->GetID(),
            'status' => $this->timeManHelper->getUserStatus(userId: $USER->GetID()),
        ];
    }

    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function addUserToBlockList(array $userData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->addUserToBlock(userData: $userData)];
    }

    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function removeUserFromBlockList(array $userData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->removeUserFromBlock(userData: $userData)];
    }

    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function updateUserBlockList(array $userData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->updateUserToBlock(userData: $userData)];
    }

    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function addOrUpdateUserBlockList(array $userData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        if (!$this->highLoadBlockHelper->isBlockedUser(userId: $userData['id'])) {
            return ['result' => $this->highLoadBlockHelper->addUserToBlock(userData: $userData)];
        }
        return ['result' => !$this->highLoadBlockHelper->isBlockedUser(userId: $userData['id'])];
    }

    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function cleanUsersBlockList(): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->cleanUsersBlockList()];
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isBlocked(int $userId): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['is_blocked' => $this->highLoadBlockHelper->isBlockedUser(userId: $userId)];

    }

}