<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Enums\Option;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use DomDigital\CustomFilters\Helpers\Crm\DealCategoryHelper;

enum OptionNameEnum
{
    case LEAD_OPTION;
    case DEAL_OPTION;
    case CONTACT_OPTION;
    case COMPANY_OPTION;

    /**
     * @param int|null $dealCategory
     *
     * @return string
     *
     * @throws ArgumentException
     */
    public function getOptionName(int $dealCategory = null): string
    {
        return match ($this) {
            self::LEAD_OPTION => 'CRM_LEAD_LIST_V12',
            self::DEAL_OPTION => self::getDealOptionName(dealCategory: $dealCategory),
            self::CONTACT_OPTION => 'CRM_CONTACT_LIST_V12',
            self::COMPANY_OPTION => 'CRM_COMPANY_LIST_V12',
        };
    }

    /**
     * @param int|null $dealCategory
     *
     * @return string
     *
     * @throws ArgumentException
     */
    private static function getDealOptionName(?int $dealCategory): string
    {
        if ($dealCategory !== null && DealCategoryHelper::existDealCategory(categoryId: $dealCategory)) {
            return 'CRM_DEAL_LIST_V12_C_' . $dealCategory;
        }

        return 'CRM_DEAL_LIST_V12';
    }

    /**
     * @param string $optionName
     *
     * @return string
     *
     * @throws ArgumentException
     */
    public static function getOptionType(string $optionName): string
    {
        return match ($optionName) {
            self::LEAD_OPTION->getOptionName() => 'lead',
            self::CONTACT_OPTION->getOptionName() => 'contact',
            self::COMPANY_OPTION->getOptionName() => 'company',
            self::DEAL_OPTION->getOptionName(
                DealCategoryHelper::getCategoryId(optionName: $optionName)
            ) => self::getDealOptionType(optionName: $optionName),
            default => 'unknown'
        };
    }

    public const ALL_OPTIONS_REGEX = 'CRM_DEAL_LIST_V12%|CRM_LEAD_LIST_V12%|CRM_CONTACT_LIST_V12%|CRM_COMPANY_LIST_V12%';

    private static function getDealOptionType(string $optionName): string
    {
        return str_contains(haystack: $optionName, needle: '_C_')
            ? 'deal_' . explode(separator: '_C_', string: $optionName)[1]
            : 'deal';
    }

    /**
     * @throws ArgumentNullException
     */
    public static function getByType(string $type): ?self
    {
        return match ($type) {
            'lead' => self::LEAD_OPTION,
            'contact' => self::CONTACT_OPTION,
            'company' => self::COMPANY_OPTION,
            'deal' => self::DEAL_OPTION,
            default => self::getDealByType(type: $type)
        };
    }

    public static function getByName(string $name): ?self
    {
        return match ($name) {
            'CRM_LEAD_LIST_V12' => self::LEAD_OPTION,
            'CRM_CONTACT_LIST_V12' => self::CONTACT_OPTION,
            'CRM_COMPANY_LIST_V12' => self::COMPANY_OPTION,
            default => self::getDealByName(name: $name)
        };
    }

    private static function getDealByName(string $name): ?self
    {
        if (str_contains(haystack: $name, needle: 'CRM_DEAL_LIST')) {
            return self::DEAL_OPTION;
        }
        return null;
    }

    public static function getDealCategoryByName(string $name): ?int
    {
        return str_contains(haystack: $name, needle: '_C_')
            ? (int)explode(separator: '_C_', string: $name)[1]
            : null;
    }


    /**
     * @throws ArgumentNullException
     */
    private static function getDealByType(string $type): self
    {
        return str_contains($type, 'deal_') ? self::DEAL_OPTION : throw new ArgumentNullException(parameter: $type);
    }

    public static function getDealCategory(string $type): ?int
    {
        return str_contains(haystack: $type, needle: '_')
            ? (int)explode(separator: '_', string: $type)[1]
            : null;
    }

}
