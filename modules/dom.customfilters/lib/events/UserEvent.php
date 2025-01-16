<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Events;

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use DomDigital\CustomFilters\Interfaces\Events\HandlerInterface;
use Starlabs\Tools\Helpers\Log;

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
     * @throws LoaderException
     */
    public static function updateBlockList(array $params): void
    {
        Loader::includeModule(moduleName: 'starlabs.tools');

        Log::createLog(data: $params, file_name: 'customfilters.log', text: 'updateBlockList.Users');
    }
}
