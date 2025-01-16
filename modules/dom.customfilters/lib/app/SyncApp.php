<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Services\Sync\SyncService;

final class SyncApp
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function start(): array
    {
        (new SyncService())->start();

        return ['result' => 'sync start'];
    }

    public function queueStart(): void
    {
        (new SyncService())->queueStart();
    }

}