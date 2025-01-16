<?php

use Bitrix\Main\Application;
use DomDigital\WorkDayBlock\Module;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Directory;
use DomDigital\WorkDayBlock\Events\WorkDayBlock;

Loc::loadMessages(file: __FILE__);

final class dom_workdayblock extends CModule
{
    private const HIGHLOAD_TABLE_NAME = 'workdayblock';
    private const HIGHLOAD_BLOCK_NAME = 'WorkDayBlock';
    private int $highLoadBlockId;
    private array $eventHandlers = [];

    public function __construct()
    {
        if (file_exists(filename: __DIR__ . '/version.php')) {
            $moduleVersion = require_once __DIR__ . '/version.php';

            $this->MODULE_ID = str_replace(search: "_", replace: ".", subject: get_class(object: $this));
            $this->MODULE_NAME = Loc::getMessage(code: 'DOM_WORKDAYBLOCK_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage(code: 'DOM_WORKDAYBLOCK_DESCRIPTION');
            $this->MODULE_GROUP_RIGHTS = 'Y';
            $this->PARTNER_NAME = Loc::getMessage(code: 'DOM_WORKDAYBLOCK_PARTNER_NAME');
            $this->PARTNER_URI = '';
            $this->MODULE_VERSION = $moduleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $moduleVersion['VERSION_DATE'];
            $this->eventHandlers = [
                [
                    'main',
                    'OnPageStart',
                    Module::class,
                    'onPageStart',
                ]
            ];
        } else {
            CAdminMessage::showMessage(
                message: Loc::getMessage(code: 'DOM_WORKDAYBLOCK_FILE_NOT_FOUND')
            );
        }
    }

    /**
     * @throws LoaderException
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        ModuleManager::registerModule(moduleName: $this->MODULE_ID);

        Loader::includeModule(moduleName: 'highloadblock');
        Loader::includeModule(moduleName: 'fileman');

        ($this->registerModule()
            && $this->createHighLoadBlock()
            && $this->createHighLoadBlockProperties()
            && $this->installEvents()
            && $this->installMenu()
            && $this->clearMenuCache()
            && $this->installFiles()
        )
            ? CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_WORKDAYBLOCK_INSTALL_SUCCESS'))
            : CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_WORKDAYBLOCK_INSTALL_FAILED'));
    }


    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */

    public function DoUninstall(): void
    {
        ModuleManager::unRegisterModule(moduleName: $this->MODULE_ID);

        Loader::includeModule(moduleName: 'highloadblock');
        Loader::includeModule(moduleName: 'fileman');

        ($this->unRegisterModule()
            && $this->deleteHighLoadBlock()
            && $this->removeEvents()
            && $this->removeMenu()
            && $this->clearMenuCache()
            && $this->unInstallFiles()
        )
            ? CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_WORKDAYBLOCK_UNINSTALL_SUCCESS'))
            : CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_WORKDAYBLOCK_UNINSTALL_FAILED'));
    }

    private function registerModule(): bool
    {
        RegisterModuleDependences(
            FROM_MODULE_ID: 'main',
            MESSAGE_ID: 'OnProlog',
            TO_MODULE_ID: $this->MODULE_ID,
            TO_CLASS: WorkDayBlock::class,
            TO_METHOD: 'onProlog'
        );

        return true;
    }

    private function unRegisterModule(): bool
    {
        CModule::IncludeModule(module_name: $this->MODULE_ID);

        UnRegisterModuleDependences(
            FROM_MODULE_ID: 'main',
            MESSAGE_ID: 'OnProlog',
            TO_MODULE_ID: $this->MODULE_ID,
            TO_CLASS: WorkDayBlock::class,
            TO_METHOD: 'onProlog'
        );

        return true;
    }

    public function installFiles(): bool
    {
        $moduleDir = explode(separator: '/', string: __DIR__);
        array_pop(array: $moduleDir);
        $moduleDir = implode(separator: '/', array: $moduleDir);
        $sourceRoot = $moduleDir . '/install/';

        $parts = [
            'components' => [
                'target' => '/bitrix/components/',
                'rewrite' => false,
            ],
            'menu' =>
            [
                'target' => '',
                'rewrite' => false,
            ]
        ];

        foreach ($parts as $dir => $config) {
            CopyDirFiles(
                path_from: $sourceRoot . $dir,
                path_to: $_SERVER['DOCUMENT_ROOT'] . $config['target'],
                ReWrite: $config['rewrite'],
                Recursive: true
            );
        }

        return true;
    }

    public function unInstallFiles(): bool
    {
        Directory::deleteDirectory(path: $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/dom.workdayblock/');
        Directory::deleteDirectory(path: $_SERVER['DOCUMENT_ROOT'] . '/workdayblock/');


        return true;
    }

    /**
     * @throws SystemException
     */
    private function createHighLoadBlock(): bool
    {
        $hbTable = HighloadBlockTable::add(
            data: [
                'NAME' => self::HIGHLOAD_BLOCK_NAME,
                'TABLE_NAME' => self::HIGHLOAD_TABLE_NAME,
            ]
        );

        $this->highLoadBlockId = $hbTable->getId();

        return (bool)$hbTable->getId();
    }

    private function createHighLoadBlockProperties(): bool
    {
        $highLoadBlockProperties = [
            'TYPE' => [
                'ENTITY_ID' => 'HLBLOCK_' . $this->highLoadBlockId, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_TYPE_ID', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => null,  // Множественное
                'MANDATORY' => 'Y', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => null, // Показывать в списке
                'EDIT_IN_LIST' => null, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => null, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0',// Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            'USER_OR_DEPARTMENT' => [
                'ENTITY_ID' => 'HLBLOCK_' . $this->highLoadBlockId, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_ID', // Код поля
                'USER_TYPE_ID' => 'integer',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => null,  // Множественное
                'MANDATORY' => 'Y', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => null, // Показывать в списке
                'EDIT_IN_LIST' => null, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => null, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0',// Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ],
            'FULL_NAME' => [
                'ENTITY_ID' => 'HLBLOCK_' . $this->highLoadBlockId, // Строковый идентификатор сущности
                'FIELD_NAME' => 'UF_FULL_NAME', // Код поля
                'USER_TYPE_ID' => 'string',  // Тип
                'XML_ID' => '', // Внешний код
                'SORT' => '100', // Сортировка
                'MULTIPLE' => null,  // Множественное
                'MANDATORY' => 'Y', // Обязательное
                'SHOW_FILTER' => 'E', // Показывать в фильтре списка
                'SHOW_IN_LIST' => null, // Показывать в списке
                'EDIT_IN_LIST' => null, // Разрешить редактирование в списке
                'IS_SEARCHABLE' => null, // Индексировать модулем поиска
                'SETTINGS' => [ // Дополнительные настройки
                    'DEFAULT_VALUE' => '', // Значение по умолчанию
                    'SIZE' => '60', // Ширина поля
                    'ROWS' => '1', // Высота поля
                    'MIN_LENGTH' => '0',// Минимальная длина строки
                    'MAX_LENGTH' => '0', // Максимальная длина строки
                    'REGEXP' => '', // Регулярное выражение для проверки значения
                ],
            ]
        ];

        foreach ($highLoadBlockProperties as $field) {
            $result = (new CUserTypeEntity)->Add(arFields: $field, bCheckUserType: false);
        }

        return (bool)$result;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private function deleteHighLoadBlock(): bool
    {
        $highLoadBlockId = HighloadBlockTable::getList(
            parameters: [
                'filter' => [
                    'NAME' => self::HIGHLOAD_BLOCK_NAME,
                    'TABLE_NAME' => self::HIGHLOAD_TABLE_NAME
                ]
            ]
        )->fetch()['ID'];

        return (bool)HighloadBlockTable::Delete(primary: $highLoadBlockId);
    }

    private function installMenu(): bool
    {
        return $this->addMenuItem(
            menuItem: [
                Loc::getMessage(code: 'DOM_WORKDAYBLOCK_TOP_MENU'),
                '/workdayblock/',
                [],
                ['menu_item_id' => 'menu_workdayblock',],
                ''
            ]
        );
    }

    private function clearMenuCache(): bool
    {
        $GLOBALS['CACHE_MANAGER']->CleanDir(table_id: 'menu');
        \CBitrixComponent::clearComponentCache(componentName: 'bitrix:menu');

        return true;
    }

    private function addMenuItem(array $menuItem): bool
    {
        if (!CModule::IncludeModule(module_name: 'fileman')) {
            return false;
        }

        $result = \CFileMan::GetMenuArray(abs_path: Application::getDocumentRoot() . '/.top.menu.php');
        $menuItems = $result['aMenuLinks'];
        $menuTemplate = $result['sMenuTemplate'];

        foreach ($menuItems as $item) {
            if ($item[1] === $menuItem[1]) {
                return false;
            }
        }
        $menuItems[] = $menuItem;

        \CFileMan::SaveMenu(
            path: ['s1', '/.top.menu.php'],
            aMenuLinksTmp: $menuItems,
            sMenuTemplateTmp: $menuTemplate
        );

        return true;
    }

    private function removeMenu(): bool
    {
        if (!CModule::IncludeModule(module_name: 'fileman')) {
            return false;
        }

        $result = CFileMan::GetMenuArray(abs_path: Application::getDocumentRoot() . '/.top.menu.php');
        $menuItems = $result['aMenuLinks'];
        $menuTemplate = $result['sMenuTemplate'];

        foreach ($menuItems as $key => $item) {
            if ($item[1] === '/workdayblock/') {
                unset($menuItems[$key]);
            }
        }

        \CFileMan::SaveMenu(
            path: ['s1', '/.top.menu.php'],
            aMenuLinksTmp: $menuItems,
            sMenuTemplateTmp: $menuTemplate
        );

        return true;
    }

    /**
     * @return bool
     */
    public function installEvents(): bool
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->eventHandlers as $handler) {
            $eventManager->registerEventHandler(
                fromModuleId: $handler[0],
                eventType: $handler[1],
                toModuleId: $this->MODULE_ID,
                toClass: $handler[2],
                toMethod: $handler[3]
            );
        }

        return true;
    }

    /**
     * @return bool
     */
    public function removeEvents(): bool
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->eventHandlers as $handler) {
            $eventManager->unRegisterEventHandler(
                fromModuleId: $handler[0],
                eventType: $handler[1],
                toModuleId: $this->MODULE_ID,
                toClass: $handler[2],
                toMethod: $handler[3]
            );
        }

        return true;
    }
}
