<?php

namespace DomDigital\WorkDayBlock\Enums;

enum HighLoadBlockTypeIdEnum: string
{
    case USER = 'user';
    case DEPARTMENT = 'department';
    case WHITELIST = 'whitelist';
}
