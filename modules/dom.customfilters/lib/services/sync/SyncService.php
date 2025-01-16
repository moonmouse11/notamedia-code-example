<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Services\Sync;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CAgent;
use DomDigital\CustomFilters\Helpers\ORM\CustomFilterHelper;
use DomDigital\CustomFilters\Helpers\Sync\SyncHelper;
use Exception;

final class SyncService
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function start(): bool
    {
        return $this->sync();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    private function sync(): bool
    {
        $customFilters = CustomFilterHelper::getList(softDelete: false);

        foreach ($customFilters as $customFilter) {
            $origin = CustomFilterHelper::getFilterOrigin(filterId: $customFilter['filter_id']);

            if ($origin !== null) {

                $customFilter = SyncHelper::compareAndUpdate(
                    origin: $origin,
                    custom: $customFilter
                );

                SyncHelper::syncFilter(customFilter: $customFilter);
            }

            SyncHelper::deleteFilter(customFilter: $customFilter);
        }

        return true;
    }

    public function queueStart(): void
    {
        CAgent::AddAgent(

        );
    }

}
