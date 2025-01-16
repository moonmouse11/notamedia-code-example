<?php

namespace DomDigital\CustomFilters\Agents;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Interfaces\Agents\AgentInterface;
use DomDigital\CustomFilters\Services\Sync\SyncService;

final class SyncAgent implements AgentInterface
{
    public const AGENT_NAME = self::class . '::runAgent();';

    /**
     * @description Run sync application agent
     *
     * @return string
     *
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function runAgent(): string
    {
        (new SyncService())->start();

        return self::AGENT_NAME;
    }
}