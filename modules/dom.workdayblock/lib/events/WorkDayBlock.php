<?php

namespace DomDigital\WorkDayBlock\Events;

use Bitrix\Main\Page\Asset;

final class WorkDayBlock
{
    public static function onProlog(): void
    {
        Asset::getInstance()->addString(
            str: '<script type="text/javascript" src="/local/modules/dom.workdayblock/files/workdayblock/main.js"></script>'
        );
    }
}
