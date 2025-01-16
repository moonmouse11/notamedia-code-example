<?php

namespace DomDigital\WorkDayBlock\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\WorkDayBlock\Helpers\DepartmentHelper;
use DomDigital\WorkDayBlock\Helpers\HighLoadBlockHelper;
use Exception;

final class DepartmentApp
{
    private HighLoadBlockHelper $highLoadBlockHelper;
    private DepartmentHelper $departmentHelper;

    public function __construct()
    {
        $this->highLoadBlockHelper = new HighLoadBlockHelper();
        $this->departmentHelper = new DepartmentHelper();
    }

    /**
     * @throws LoaderException
     */
    public function getDepartmentList(): array
    {
        Loader::includeModule(moduleName: 'iblock');

        return $this->departmentHelper->getDepartmentsList();
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getDepartmentBlockList(): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return $this->highLoadBlockHelper->getBlockedDepartments();
    }

    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function addDepartmentToBlockList(array $departmentData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->addDepartmentToBlock(departmentData: $departmentData)];
    }


    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function removeDepartmentFromBlockList(array $departmentData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->removeDepartmentFromBlock(departmentData: $departmentData)];
    }

    /**
     * @throws LoaderException
     * @throws Exception
     */
    public function updateDepartmentInBlockList(array $departmentData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->updateDepartmentToBlock(departmentData: $departmentData)];
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public function addOrUpdateDepartmentBlockList(array $departmentData): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        if(!$this->highLoadBlockHelper->isBlockedDepartment(departmentIds: [$departmentData['id']]))  {
            return ['result' => $this->highLoadBlockHelper->addDepartmentToBlock(departmentData: $departmentData)];
        }
        return ['result' => !$this->highLoadBlockHelper->isBlockedDepartment(departmentIds: [$departmentData['id']])];
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function cleanDepartmentBlockList(): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['result' => $this->highLoadBlockHelper->cleanDepartmentsBlockList()];
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function isBlocked(int $departmentId): array
    {
        Loader::includeModule(moduleName: 'highloadblock');

        return ['is_blocked' => $this->highLoadBlockHelper->isBlockedDepartment(departmentIds: [$departmentId])];

    }
}
