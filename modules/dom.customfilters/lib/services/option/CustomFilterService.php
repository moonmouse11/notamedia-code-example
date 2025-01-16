<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Services\Option;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Helpers\ORM\CustomFilterHelper;

final class CustomFilterService
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getById(string $filterId): array
    {
        return CustomFilterHelper::getById(filterId: $filterId);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getList(int $userId = null): array
    {
        return CustomFilterHelper::getList(authorId: $userId);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function updateCustomFilter(string $filterId): bool
    {
        $origin = CustomFilterHelper::getFilterOrigin(filterId: $filterId);

        return CustomFilterHelper::update(
            filter: $origin,
            optionNameEnum: OptionNameEnum::getByType(type: $origin['type']),
            authorId: (int)$origin['author_id'],
        );
    }

}