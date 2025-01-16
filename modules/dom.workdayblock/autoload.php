<?php

use DomDigital\WorkDayBlock\Ajax\Controller\App;
use DomDigital\WorkDayBlock\Events\DepartmentEvent;
use Bitrix\Main\Loader;
use DomDigital\WorkDayBlock\Ajax\Controller\Department;
use DomDigital\WorkDayBlock\Ajax\Controller\Test;
use DomDigital\WorkDayBlock\Ajax\Controller\User;
use DomDigital\WorkDayBlock\App\DepartmentApp;
use DomDigital\WorkDayBlock\App\UserApp;
use DomDigital\WorkDayBlock\Enums\HighLoadBlockTypeIdEnum;
use DomDigital\WorkDayBlock\Events\UserEvent;
use DomDigital\WorkDayBlock\Events\WorkDayBlock;
use DomDigital\WorkDayBlock\Helpers\DepartmentHelper;
use DomDigital\WorkDayBlock\Helpers\HighLoadBlockHelper;
use DomDigital\WorkDayBlock\Helpers\TimeManHelper;
use DomDigital\WorkDayBlock\Helpers\UserHelper;
use DomDigital\WorkDayBlock\Interface\HandlerInterface;
use DomDigital\WorkDayBlock\Module;

Loader::registerAutoLoadClasses(moduleName: 'dom.workdayblock', classes: [
    WorkDayBlock::class => 'lib/events/WorkDayBlock.php',
    UserEvent::class => 'lib/events/UserEvent.php',
    DepartmentEvent::class => 'lib/events/DepartmentEvent.php',
    Test::class => 'lib/ajax/controller/Test.php',
    User::class => 'lib/ajax/controller/User.php',
    App::class => 'lib/ajax/controller/App.php',
    Department::class => 'lib/ajax/controller/Department.php',
    UserApp::class => 'lib/app/UserApp.php',
    DepartmentApp::class => 'lib/app/DepartmentApp.php',
    UserHelper::class => 'lib/helpers/UserHelper.php',
    DepartmentHelper::class => 'lib/helpers/DepartmentHelper.php',
    HighLoadBlockHelper::class => 'lib/helpers/HighLoadBlockHelper.php',
    TimeManHelper::class => 'lib/helpers/TimeManHelper.php',
    HandlerInterface::class => 'lib/interface/HandlerInterface.php',
    HighLoadBlockTypeIdEnum::class => 'lib/enums/HighLoadBlockTypeIdEnum.php',
    Module::class => 'lib/Module.php',
]);
