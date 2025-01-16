<?php

namespace DomDigital\WorkDayBlock\Events;

use Bitrix\Main\EventManager;
use DomDigital\WorkDayBlock\Helpers\HighLoadBlockHelper;
use DomDigital\WorkDayBlock\Helpers\UserHelper;
use DomDigital\WorkDayBlock\Interface\HandlerInterface;
use Exception;

final class UserEvent implements HandlerInterface
{

    public static function setHandlers(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            fromModuleId: 'main',
            eventType: 'OnBeforeUserUpdate',
            callback: [self::class, 'updateBlockList']
        );
    }

    /**
     * @throws Exception
     */
    public static function updateBlockList(array &$data): void
    {
        $user = UserHelper::getUserData(userId: $data['ID']);

        $highLoadBlockHelper = new HighLoadBlockHelper();

        $blockedUsers = array_column(array: $highLoadBlockHelper->getBlockedUsers(), column_key: 'id');

        if (in_array(needle: $user['ID'], haystack: $blockedUsers, strict: false)) {

            $cleanData = [
                'id' => $user['ID'],
                'full_name' => UserHelper::getFullNameFormat(userData: $user),
            ];

            $highLoadBlockHelper->updateUserToBlock(userData: $cleanData);
        }
    }
}
