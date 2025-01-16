<?php

namespace DomDigital\WorkDayBlock\Events;

use Bitrix\Main\EventManager;
use DomDigital\WorkDayBlock\Helpers\DepartmentHelper;
use DomDigital\WorkDayBlock\Helpers\HighLoadBlockHelper;
use DomDigital\WorkDayBlock\Interface\HandlerInterface;
use Exception;

final class DepartmentEvent implements HandlerInterface
{
    public static function setHandlers(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            fromModuleId: 'iblock',
            eventType: 'OnAfterIBlockSectionUpdate',
            callback: [self::class, 'updateBlockList']
        );
        $eventManager->addEventHandler(
            fromModuleId: 'iblock',
            eventType: 'OnAfterIBlockSectionDelete',
            callback: [self::class, 'cleanBlockList']
        );
    }

    /**
     * @throws Exception
     */
    public static function updateBlockList(array &$data): void
    {
        $departmentData = DepartmentHelper::getDepartmentData(departmentId: $data['ID']);

        $highLoadBlockHelper = new HighLoadBlockHelper();

        $blockedDepartments = array_column(array: $highLoadBlockHelper->getBlockedDepartments(), column_key: 'id');

        if (in_array(needle: $data['ID'], haystack:  $blockedDepartments, strict: false)) {
            $cleanData = [
                'id' => $departmentData['ID'],
                'title' => $departmentData['NAME'],
            ];

            $highLoadBlockHelper->updateDepartmentToBlock(departmentData: $cleanData);
        }
    }

    /**
     * @throws Exception
     */
    public static function cleanBlockList(array &$data): void
    {
        $highLoadBlockHelper = new HighLoadBlockHelper();

        $blockedDepartments = array_column(array: $highLoadBlockHelper->getBlockedDepartments(), column_key: 'id');

        if (in_array(needle: $data['ID'], haystack: $blockedDepartments, strict: false)) {
            $cleanData = ['id' => $data['ID']];

            $highLoadBlockHelper->removeDepartmentFromBlock(departmentData: $cleanData);
        }
    }
}