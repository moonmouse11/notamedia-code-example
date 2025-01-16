<?php

namespace DomDigital\WorkDayBlock\Helpers;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use DomDigital\WorkDayBlock\enums\HighLoadBlockTypeIdEnum;
use Exception;

final class HighLoadBlockHelper
{
    private const HIGHLOAD_TABLE_NAME = 'workdayblock';
    private const HIGHLOAD_BLOCK_NAME = 'WorkDayBlock';
    private string|DataManager $dataClass;

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function __construct()
    {
        $hbTable = HighloadBlockTable::getById(id: self::getHighLoadBlockId())->fetch();
        $entity = HighloadBlockTable::compileEntity(hlblock: $hbTable);

        $this->dataClass = $entity->getDataClass();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private static function getHighLoadBlockId(): int
    {
        return HighloadBlockTable::getList(
            parameters: [
                'filter' => [
                    'NAME' => self::HIGHLOAD_BLOCK_NAME,
                    'TABLE_NAME' => self::HIGHLOAD_TABLE_NAME
                ]
            ]
        )->fetch()['ID'];
    }


    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getBlockedUsers(): array|false
    {
        return array_map(
            (static fn($user) => [
                'id' => $user['UF_ID'],
                'full_name' => $user['UF_FULL_NAME']
            ]),
            $this->dataClass::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        ['UF_TYPE_ID' => HighLoadBlockTypeIdEnum::USER->value]
                    ]
                ]
            )->fetchAll()
        );
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getBlockedDepartments(): array|false
    {
        return array_map(
            callback: (static fn($department) => [
                'id' => $department['UF_ID'],
                'title' => $department['UF_FULL_NAME']
            ]),
            array: $this->dataClass::getList(
                parameters: [
                    'select' => ['*'],
                    'filter' => [
                        ['UF_TYPE_ID' => HighLoadBlockTypeIdEnum::DEPARTMENT->value]
                    ]
                ]
            )->fetchAll()
        );
    }

    /**
     * @throws Exception
     */
    public function addUserToBlock(array $userData): bool
    {
        return $this->dataClass::add(
            data: [
                'UF_ID' => $userData['id'],
                'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::USER->value,
                'UF_FULL_NAME' => $userData['full_name'],
            ]
        )->isSuccess();
    }


    /**
     * @throws Exception
     */
    public function addDepartmentToBlock(array $departmentData): bool
    {
        return $this->dataClass::add(
            data: [
                'UF_ID' => $departmentData['id'],
                'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::DEPARTMENT->value,
                'UF_FULL_NAME' => $departmentData['title'],
            ]
        )->isSuccess();
    }

    /**
     * @throws Exception
     */
    public function removeUserFromBlock(array $userData): bool
    {
        $removedData = $this->dataClass::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    [
                        'UF_ID' => $userData['id'],
                        'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::USER->value
                    ]
                ]
            ]
        )->fetch();

        return $this->dataClass::delete(primary: $removedData['ID'])->isSuccess();
    }


    /**
     * @throws Exception
     */
    public function removeDepartmentFromBlock(array $departmentData): bool
    {
        $removedData = $this->dataClass::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    [
                        'UF_ID' => $departmentData['id'],
                        'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::DEPARTMENT->value
                    ]
                ]
            ]
        )->fetch();

        return $this->dataClass::delete(primary: $removedData['ID'])->isSuccess();
    }

    /**
     * @throws Exception
     */
    public function updateUserToBlock(array $userData): bool
    {
        $oldData = $this->dataClass::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    [
                        'UF_ID' => $userData['id'],
                        'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::USER->value
                    ]
                ]
            ]
        )->fetch();

        return $this->dataClass::update(
            primary: $oldData['ID'],
            data: [
                'UF_ID' => $userData['id'],
                'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::USER->value,
                'UF_FULL_NAME' => $userData['full_name'],
            ]
        )->isSuccess();
    }


    /**
     * @throws Exception
     */
    public function updateDepartmentToBlock(array $departmentData): bool
    {
        $oldData = $this->dataClass::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    [
                        'UF_ID' => $departmentData['id'],
                        'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::DEPARTMENT->value
                    ]
                ]
            ]
        )->fetch();

        return $this->dataClass::update(
            primary: $oldData['ID'],
            data: [
                'UF_ID' => $departmentData['id'],
                'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::DEPARTMENT->value,
                'UF_FULL_NAME' => $departmentData['title'],
            ]
        )->isSuccess();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function isBlocked(int $userId, array $departmentIds): bool
    {
        return $this->isBlockedUser(userId: $userId) || $this->isBlockedDepartment(departmentIds: $departmentIds);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function isBlockedUser(int $userId): bool
    {
        return (bool)$this->dataClass::getList(
            parameters: [
                'filter' => [
                    'UF_ID' => $userId,
                    'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::USER->value
                ]
            ]
        )->fetch();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function isBlockedDepartment(array $departmentIds): bool
    {
        return (bool)$this->dataClass::getList(
            parameters: [
                'filter' => [
                    'UF_ID' => $departmentIds,
                    'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::DEPARTMENT->value
                ]
            ]
        )->fetch();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public function cleanUsersBlockList(): bool
    {
        $result = true;

        $records = $this->dataClass::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::USER->value
                ]
            ])->fetchAll();

        foreach ($records as $record) {
            $result = $this->dataClass::delete(primary: $record['ID'])->isSuccess();
        }

        return $result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public function cleanDepartmentsBlockList(): bool
    {
        $result = true;

        $records = $this->dataClass::getList(
            parameters: [
                'select' => ['*'],
                'filter' => [
                    'UF_TYPE_ID' => HighLoadBlockTypeIdEnum::DEPARTMENT->value
                ]
            ])->fetchAll();

        foreach ($records as $record) {
            $result = $this->dataClass::delete(primary: $record['ID'])->isSuccess();
        }

        return $result;
    }

}
