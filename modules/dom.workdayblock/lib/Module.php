<?php

namespace DomDigital\WorkDayBlock;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use DomDigital\WorkDayBlock\Events\DepartmentEvent;
use DomDigital\WorkDayBlock\Events\UserEvent;
use Exception;

final class Module
{
    /**
     * Обработчик начала отображения страницы
     * @return void
     * @throws Exception
     * @throws LoaderException
     */
    public static function onPageStart(): void
    {
        Loader::includeModule(moduleName: 'dom.workdayblock');

        self::setupEventHandlers();
    }

    /**
     * Обработчики событий.
     */
    protected static function setupEventHandlers(): void
    {
        DepartmentEvent::setHandlers();
        UserEvent::setHandlers();
    }
}
