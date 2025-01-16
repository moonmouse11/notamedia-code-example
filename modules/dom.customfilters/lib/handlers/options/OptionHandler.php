<?php

declare(strict_types=1);

namespace DomDigital\CustomFilters\Handlers\Options;

final class OptionHandler
{
    /**
     * @description Handle B24 option from CUserOptions
     *
     * @param array $option
     *
     * @return array
     */
    public static function handle(array $option): array
    {
        return [
            'id' => $option['ID'],
            'name' => $option['NAME'],
            'user_id' => $option['USER_ID'],
            'category' => $option['CATEGORY'],
            'common' => $option['COMMON'],
            'value' => unserialize(
                data: $option['VALUE'],
                options: ['allowed_classes' => false]
            )
        ];
    }

    /**
     * @description Prepare B24 option for CUserOptions
     *
     * @param array $option
     *
     * @return array
     */
    public static function prepare(array $option): array
    {
        return [
            'ID' => $option['id'],
            'NAME' => $option['name'],
            'USER_ID' => $option['user_id'],
            'CATEGORY' => $option['category'],
            'COMMON' => $option['common'],
            'VALUE' => serialize(value: $option['value'])
        ];
    }
}