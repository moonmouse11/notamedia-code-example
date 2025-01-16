<?php

use Bitrix\Main\Loader;

if (!defined(constant_name: 'B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (!Loader::includeModule(moduleName: 'dom.customfilters')) {
    return false;
}

final class CustomFiltersSettings extends CBitrixComponent
{
    public function executeComponent(): void
    {
        $this->includeComponentTemplate();
    }
}