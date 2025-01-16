<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Enums\Crm;

enum EntityTypeEnum: string
{
    case DEAL = 'deal';
    case LEAD = 'lead';
    case CONTACT = 'contact';
    case COMPANY = 'company';
}
