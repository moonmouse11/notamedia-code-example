<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use DomDigital\CustomFilters\Events\DepartmentEvent;
use DomDigital\CustomFilters\Events\UserEvent;
use Exception;

final class Module
{
    /**
     * @description Обработчик начала отображения страницы
     *
     * @return void
     *
     * @throws Exception
     * @throws LoaderException
     */
    public static function onPageStart(): void
    {
        Loader::includeModule(moduleName: 'dom.customfilters');

        self::setupEventHandlers();
    }

    /**
     * @description Обработчики событий.
     *
     * @return void
     */
    protected static function setupEventHandlers(): void
    {
        DepartmentEvent::setHandlers();
        UserEvent::setHandlers();
    }
}
