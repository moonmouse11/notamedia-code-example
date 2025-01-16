<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\App;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use DomDigital\CustomFilters\Enums\Crm\EntityTypeEnum;
use DomDigital\CustomFilters\Helpers\Crm\Fields\CompanyFieldsHelper;
use DomDigital\CustomFilters\Helpers\Crm\Fields\ContactFieldsHelper;
use DomDigital\CustomFilters\Helpers\Crm\Fields\DealFieldsHelper;
use DomDigital\CustomFilters\Helpers\Crm\Fields\LeadFieldsHelper;

final class UserFieldApp
{
    /**
     * @description Method for getting list B24 userfields data
     *
     * @param string $entityType
     *
     * @return array
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getList(string $entityType = ''): array
    {
        return match ($entityType) {
            EntityTypeEnum::LEAD->value => ['result' => (new LeadFieldsHelper())->getEntityFieldsMap()],
            EntityTypeEnum::DEAL->value => ['result' => (new DealFieldsHelper())->getEntityFieldsMap()],
            EntityTypeEnum::CONTACT->value => ['result' => (new ContactFieldsHelper())->getEntityFieldsMap()],
            EntityTypeEnum::COMPANY->value => ['result' => (new CompanyFieldsHelper())->getEntityFieldsMap()],
            default => [
                'result' => [
                    EntityTypeEnum::LEAD->value => (new LeadFieldsHelper())->getEntityFieldsMap(),
                    EntityTypeEnum::DEAL->value => (new DealFieldsHelper())->getEntityFieldsMap(),
                    EntityTypeEnum::CONTACT->value => (new ContactFieldsHelper())->getEntityFieldsMap(),
                    EntityTypeEnum::COMPANY->value => (new CompanyFieldsHelper())->getEntityFieldsMap(),
                ]
            ],
        };
    }

}