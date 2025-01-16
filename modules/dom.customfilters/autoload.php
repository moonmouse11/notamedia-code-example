<?php

use DomDigital\CustomFilters\Handlers\Ajax\AjaxInputHandler;
use DomDigital\CustomFilters\Handlers\Options\FilterHandler;
use DomDigital\CustomFilters\Handlers\Options\OptionHandler;
use DomDigital\CustomFilters\Handlers\Options\SwitchAssigmentHandler;
use DomDigital\CustomFilters\Helpers\Crm\DealCategoryHelper;
use DomDigital\CustomFilters\Helpers\Crm\Fields\CompanyFieldsHelper;
use DomDigital\CustomFilters\Helpers\Crm\Fields\ContactFieldsHelper;
use DomDigital\CustomFilters\Helpers\Crm\Fields\DealFieldsHelper;
use DomDigital\CustomFilters\Helpers\Crm\Fields\LeadFieldsHelper;
use DomDigital\CustomFilters\Helpers\ORM\CustomFilterHelper;
use DomDigital\CustomFilters\Helpers\ORM\FilterAccessHelper;
use DomDigital\CustomFilters\Helpers\Option\FilterHelper;
use DomDigital\CustomFilters\Helpers\Option\OptionHelper;
use DomDigital\CustomFilters\Helpers\Structure\AccessHelper;
use DomDigital\CustomFilters\Helpers\Structure\DepartmentHelper;
use DomDigital\CustomFilters\Helpers\Structure\UserHelper;
use DomDigital\CustomFilters\Helpers\Sync\SyncHelper;
use DomDigital\CustomFilters\Interfaces\Agents\AgentInterface;
use DomDigital\CustomFilters\Interfaces\Crm\EntityFieldsInterface;
use DomDigital\CustomFilters\Interfaces\Events\HandlerInterface;
use DomDigital\CustomFilters\ORM\Entities\Tables\AccessTable;
use DomDigital\CustomFilters\ORM\Entities\Tables\FilterRelationTable;
use DomDigital\CustomFilters\ORM\Entities\Tables\FilterTable;
use DomDigital\CustomFilters\Services\Crm\UserFieldService;
use DomDigital\CustomFilters\Services\Option\CustomFilterService;
use DomDigital\CustomFilters\Services\Option\FilterAccessService;
use DomDigital\CustomFilters\Services\Option\FilterService;
use DomDigital\CustomFilters\Services\Structure\AccessService;
use DomDigital\CustomFilters\Services\Structure\DepartmentService;
use DomDigital\CustomFilters\Services\Structure\UserService;
use DomDigital\CustomFilters\Services\Sync\SyncService;
use DomDigital\CustomFilters\Events\UserEvent;
use DomDigital\CustomFilters\Events\DepartmentEvent;
use DomDigital\CustomFilters\Module;
use DomDigital\CustomFilters\Enums\Option\OptionNameEnum;
use DomDigital\CustomFilters\Enums\Option\OptionCategoryEnum;
use DomDigital\CustomFilters\Enums\ORM\AccessEntityTypeEnum;
use DomDigital\CustomFilters\Enums\Access\RoleAccessEnum;
use DomDigital\CustomFilters\App\UserFieldApp;
use DomDigital\CustomFilters\App\UserApp;
use DomDigital\CustomFilters\App\SyncApp;
use DomDigital\CustomFilters\App\FunnelApp;
use DomDigital\CustomFilters\App\FilterApp;
use DomDigital\CustomFilters\App\FilterAccessApp;
use DomDigital\CustomFilters\App\DepartmentApp;
use DomDigital\CustomFilters\Ajax\Controller\Funnel;
use DomDigital\CustomFilters\Ajax\Controller\FilterAccess;
use DomDigital\CustomFilters\Ajax\Controller\Filter;
use DomDigital\CustomFilters\Ajax\Controller\Department;
use DomDigital\CustomFilters\Ajax\Controller\Access;
use DomDigital\CustomFilters\Agents\SyncAgent;
use DomDigital\CustomFilters\Ajax\Controller\Sync;
use DomDigital\CustomFilters\Ajax\Controller\Test;
use DomDigital\CustomFilters\App\AccessApp;
use DomDigital\CustomFilters\Ajax\Controller\UserField;
use DomDigital\CustomFilters\Ajax\Controller\User;
use DomDigital\CustomFilters\Enums\Crm\EntityTypeEnum;
use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(moduleName: 'dom.customfilters',
    classes: [
        SyncAgent::class => 'lib/agents/SyncAgent.php',
        Access::class => 'lib/ajax/controller/Access.php',
        Department::class => 'lib/ajax/controller/Department.php',
        Filter::class => 'lib/ajax/controller/Filter.php',
        FilterAccess::class => 'lib/ajax/controller/FilterAccess.php',
        Funnel::class => 'lib/ajax/controller/Funnel.php',
        Sync::class => 'lib/ajax/controller/Sync.php',
        Test::class => 'lib/ajax/controller/Test.php',
        User::class => 'lib/ajax/controller/User.php',
        UserField::class => 'lib/ajax/controller/UserField.php',
        AccessApp::class => 'lib/app/AccessApp.php',
        DepartmentApp::class => 'lib/app/DepartmentApp.php',
        FilterAccessApp::class => 'lib/app/FilterAccessApp.php',
        FilterApp::class => 'lib/app/FilterApp.php',
        FunnelApp::class => 'lib/app/FunnelApp.php',
        SyncApp::class => 'lib/app/SyncApp.php',
        UserApp::class => 'lib/app/UserApp.php',
        UserFieldApp::class => 'lib/app/UserFieldApp.php',
        RoleAccessEnum::class => 'lib/enums/access/RoleAccessEnum.php',
        EntityTypeEnum::class => 'lib/enums/crm/EntityTypeEnum.php',
        AccessEntityTypeEnum::class => 'lib/enums/orm/AccessEntityTypeEnum.php',
        OptionCategoryEnum::class => 'lib/enums/option/OptionCategoryEnum.php',
        OptionNameEnum::class => 'lib/enums/option/OptionNameEnum.php',
        DepartmentEvent::class => 'lib/events/DepartmentEvent.php',
        UserEvent::class => 'lib/events/UserEvent.php',
        AjaxInputHandler::class => 'lib/handlers/ajax/AjaxInputHandler.php',
        FilterHandler::class => 'lib/handlers/options/FilterHandler.php',
        OptionHandler::class => 'lib/handlers/options/OptionHandler.php',
        SwitchAssigmentHandler::class => 'lib/handlers/options/SwitchAssigmentHandler.php',
        DealCategoryHelper::class => 'lib/helpers/crm/DealCategoryHelper.php',
        CompanyFieldsHelper::class => 'lib/helpers/crm/fields/CompanyFieldsHelper.php',
        ContactFieldsHelper::class => 'lib/helpers/crm/fields/ContactFieldsHelper.php',
        DealFieldsHelper::class => 'lib/helpers/crm/fields/DealFieldsHelper.php',
        LeadFieldsHelper::class => 'lib/helpers/crm/fields/LeadFieldsHelper.php',
        DomDigital\CustomFilters\Helpers\ORM\AccessHelper::class => 'lib/helpers/orm/AccessHelper.php',
        CustomFilterHelper::class => 'lib/helpers/orm/CustomFilterHelper.php',
        FilterAccessHelper::class => 'lib/helpers/orm/FilterAccessHelper.php',
        FilterHelper::class => 'lib/helpers/option/FilterHelper.php',
        OptionHelper::class => 'lib/helpers/option/OptionHelper.php',
        AccessHelper::class => 'lib/helpers/structure/AccessHelper.php',
        DepartmentHelper::class => 'lib/helpers/structure/DepartmentHelper.php',
        UserHelper::class => 'lib/helpers/structure/UserHelper.php',
        SyncHelper::class => 'lib/helpers/sync/SyncHelper.php',
        AgentInterface::class => 'lib/interfaces/agents/AgentInterface.php',
        EntityFieldsInterface::class => 'lib/interfaces/crm/EntityFieldsInterface.php',
        HandlerInterface::class => 'lib/interfaces/events/HandlerInterface.php',
        Module::class => 'lib/Module.php',
        AccessTable::class => 'lib/orm/entities/tables/AccessTable.php',
        FilterRelationTable::class => 'lib/orm/entities/tables/FilterRelationTable.php',
        FilterTable::class => 'lib/orm/entities/tables/FilterTable.php',
        UserFieldService::class => 'lib/services/crm/UserFieldService.php',
        CustomFilterService::class => 'lib/services/option/CustomFilterService.php',
        FilterAccessService::class => 'lib/services/option/FilterAccessService.php',
        FilterService::class => 'lib/services/option/FilterService.php',
        AccessService::class => 'lib/services/structure/AccessService.php',
        DepartmentService::class => 'lib/services/structure/DepartmentService.php',
        UserService::class => 'lib/services/structure/UserService.php',
        SyncService::class => 'lib/services/sync/SyncService.php',
    ]
);
