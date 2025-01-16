<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use DomDigital\CustomFilters\Helpers\Crm\DealCategoryHelper;

final class FunnelApp
{

    /**
     * @throws ArgumentException
     */
    public function getList(): array
    {
        return ['result' => DealCategoryHelper::getDealCategoriesMap()];
    }

}