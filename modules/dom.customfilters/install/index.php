<?php

use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\Entity\TextField;
use Bitrix\Main\Type\DateTime;
use DomDigital\CustomFilters\Agents\SyncAgent;
use DomDigital\CustomFilters\Module;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\Directory;


Loc::loadMessages(file: __FILE__);

final class dom_customfilters extends CModule
{
    private const TABLES_NAME = [
        'access' => 'dom_cf_accesses',
        'filter' => 'dom_cf_filters',
        'filter_relation' => 'dom_cf_filter_relation'
    ];
    private array $eventHandlers = [];

    public function __construct()
    {
        if (file_exists(filename: __DIR__ . '/version.php')) {
            $moduleVersion = require_once __DIR__ . '/version.php';

            $this->MODULE_ID = str_replace(search: "_", replace: ".", subject: get_class(object: $this));
            $this->MODULE_NAME = Loc::getMessage(code: 'DOM_CUSTOMFILTERS_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage(code: 'DOM_CUSTOMFILTERS_DESCRIPTION');
            $this->MODULE_GROUP_RIGHTS = 'Y';
            $this->PARTNER_NAME = Loc::getMessage(code: 'DOM_CUSTOMFILTERS_PARTNER_NAME');
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
            CAdminMessage::showMessage(message: Loc::getMessage(code: 'DOM_CUSTOMFILTERS_FILE_NOT_FOUND'));
        }
    }

    /**
     * @throws LoaderException
     */
    public function DoInstall(): void
    {
        ModuleManager::registerModule(moduleName: $this->MODULE_ID);

        Loader::includeModule(moduleName: 'fileman');

        ($this->installEvents()
            && $this->createDatabaseTables()
            && $this->installFiles()
            && $this->installMenu()
            && $this->clearMenuCache()
            && $this->installAgent()
        )
            ? CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_CUSTOMFILTERS_INSTALL_SUCCESS'))
            : CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_CUSTOMFILTERS_INSTALL_FAILED'));
    }


    /**
     * @throws LoaderException
     * @throws SqlQueryException
     */

    public function DoUninstall(): void
    {
        Loader::includeModule(moduleName: 'fileman');

        ($this->removeEvents()
            && $this->removeAgent()
            && $this->removeMenu()
            && $this->clearMenuCache()
            && $this->unInstallFiles()
            && $this->dropDatabaseTables()
        )
            ? CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_CUSTOMFILTERS_UNINSTALL_SUCCESS'))
            : CAdminMessage::ShowNote(message: Loc::getMessage(code: 'DOM_CUSTOMFILTERS_UNINSTALL_FAILED'));

        ModuleManager::unRegisterModule(moduleName: $this->MODULE_ID);

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
        Directory::deleteDirectory(path: $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/dom.customfilters/');
        Directory::deleteDirectory(path: $_SERVER['DOCUMENT_ROOT'] . '/customfilters/');


        return true;
    }

    private function installMenu(): bool
    {
        return $this->addMenuItem(
            menuItem: [
                Loc::getMessage(code: 'DOM_CUSTOMFILTERS_TOP_MENU'),
                '/customfilters/',
                [],
                ['menu_item_id' => 'menu_customfilters',],
                ''
            ]
        );
    }

    private function clearMenuCache(): bool
    {
        $GLOBALS['CACHE_MANAGER']->CleanDir(table_id: 'menu');
        CBitrixComponent::clearComponentCache(componentName: 'bitrix:menu');

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
            if ($item[1] === '/customfilters/') {
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

    private function createDatabaseTables(): bool
    {
        $connection = Application::getConnection();

        if (!$connection->isTableExists(tableName: self::TABLES_NAME['access'])) {
            try {
                $connection->createTable(
                    tableName: self::TABLES_NAME['access'],
                    fields: [
                        'id' => new IntegerField(
                            name: 'id',
                            parameters: [
                                'column_name' => 'id',
                                'primary' => true,
                                'autocomplete' => true,
                                'unique' => true
                            ]
                        ),
                        'role' => new StringField(
                            name: 'role',
                            parameters: [
                                'required' => false,
                                'nullable' => true,
                                'size' => 100,
                            ]
                        ),
                        'entity_type' => new StringField(
                            name: 'entity_type',
                            parameters: [
                                'required' => true,
                                'nullable' => false,
                                'size' => 100,
                            ]
                        ),
                        'entity_id' => new IntegerField(
                            name: 'entity_id',
                            parameters: [
                                'column_name' => 'entity_id',
                                'nullable' => false,
                                'required' => true
                            ]
                        ),
                        'name' => new StringField(
                            name: 'name',
                            parameters: [
                                'column_name' => 'name',
                                'nullable' => false,
                                'required' => true,
                                'size' => 150,
                            ]
                        ),
                        'active' => new BooleanField(
                            name: 'active',
                            parameters: [
                                'column_name' => 'active',
                                'nullable' => false,
                                'required' => false,
                                'default' => true
                            ]
                        )
                    ],
                    primary: ['id'],
                    autoincrement: ['id']
                );
            } catch (Exception $exception) {
                return false;
            }
        }

        if (!$connection->isTableExists(tableName: self::TABLES_NAME['filter'])) {
            try {
                $connection->createTable(
                    tableName: self::TABLES_NAME['filter'],
                    fields: [
                        'id' => new IntegerField(
                            name: 'id',
                            parameters: [
                                'column_name' => 'id',
                                'primary' => true,
                                'autocomplete' => true,
                                'unique' => true
                            ]
                        ),
                        'filter_id' => new StringField(
                            name: 'filter_id',
                            parameters: [
                                'column_name' => 'filter_id',
                                'nullable' => false,
                                'required' => true,
                                'size' => 150,
                            ]
                        ),
                        'name' => new StringField(
                            name: 'name',
                            parameters: [
                                'column_name' => 'name',
                                'nullable' => false,
                                'required' => true,
                                'size' => 200,
                            ]
                        ),
                        'option_name' => new StringField(
                            name: 'option_name',
                            parameters: [
                                'nullable' => false,
                                'required' => true,
                                'size' => 200,
                            ]
                        ),
                        'category' => new StringField(
                            name: 'category',
                            parameters: [
                                'column_name' => 'category',
                                'nullable' => false,
                                'required' => true,
                                'size' => 100,
                            ]
                        ),
                        'author_id' => new IntegerField(
                            name: 'author_id',
                            parameters: [
                                'nullable' => false,
                                'required' => true,
                            ]
                        ),
                        'value' => new TextField(
                            name: 'value',
                            parameters: [
                                'column_name' => 'value',
                                'nullable' => false,
                                'required' => true,
                            ]
                        ),
                        'common' => new BooleanField(
                            name: 'common',
                            parameters: [
                                'column_name' => 'common',
                                'required' => false,
                                'default' => false
                            ]
                        ),
                        'soft_delete' => new BooleanField(
                            name: 'soft_delete',
                            parameters: [
                                'column_name' => 'soft_delete',
                                'required' => false,
                                'default' => false
                            ]
                        )
                    ],
                    primary: ['id'],
                    autoincrement: ['id']
                );
            } catch (Exception $exception) {
                return false;
            }
        }

        if (!$connection->isTableExists(tableName: self::TABLES_NAME['filter_relation'])) {
            try {
                $connection->createTable(
                    tableName: self::TABLES_NAME['filter_relation'],
                    fields: [
                        'id' => new IntegerField(
                            name: 'id',
                            parameters: [
                                'column_name' => 'id',
                                'primary' => true,
                                'unique' => true,
                                'autocomplete' => true,
                            ]
                        ),
                        'filter_id' => new StringField(
                            name: 'filter_id',
                            parameters: [
                                'column_name' => 'filter_id',
                                'nullable' => false,
                                'required' => true,
                                'size' => 150,
                            ]
                        ),
                        'filter_type' => new StringField(
                            name: 'filter_type',
                            parameters: [
                                'column_name' => 'filter_type',
                                'nullable' => false,
                                'required' => true,
                                'size' => 150,
                            ]
                        ),
                        'entity_type' => new StringField(
                            name: 'entity_type',
                            parameters: [
                                'column_name' => 'entity_type',
                                'nullable' => false,
                                'required' => true,
                                'size' => 100,
                            ]
                        ),
                        'entity_id' => new IntegerField(
                            name: 'entity_id',
                            parameters: [
                                'column_name' => 'entity_id',
                                'nullable' => false,
                                'required' => true,
                            ]
                        ),
                    ],
                    primary: ['id'],
                    autoincrement: ['id']
                );
            } catch (Exception $exception) {
                return false;
            }
        }
        return true;
    }

    /**
     * @throws SqlQueryException
     */
    private function dropDatabaseTables(): bool
    {
        $connection = Application::getConnection();

        if ($connection->isTableExists(tableName: self::TABLES_NAME['access'])) {
            $connection->dropTable(tableName: self::TABLES_NAME['access']);
        }

        if ($connection->isTableExists(tableName: self::TABLES_NAME['filter'])) {
            $connection->dropTable(tableName: self::TABLES_NAME['filter']);
        }

        if ($connection->isTableExists(tableName: self::TABLES_NAME['filter_relation'])) {
            $connection->dropTable(tableName: self::TABLES_NAME['filter_relation']);
        }

        return true;
    }

    /**
     * @description Install B24 events
     *
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
     * @description Remove B24 events
     *
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

    /**
     * @throws LoaderException
     */
    private function installAgent(): bool
    {
        Loader::includeModule(moduleName: $this->MODULE_ID);

        return (bool)CAgent::AddAgent(
            name: SyncAgent::AGENT_NAME,
            module: $this->MODULE_ID,
            period: 'Y',
            interval: 86400,
            datecheck: (new DateTime())->add(interval: '1 day')
                ->setTime(hour: 00, minute: 00)->format(format: 'd.m.Y H:i:s'),
            active: 'Y',
            next_exec: (new DateTime())->add(interval: '1 day')
                ->setTime(hour: 00, minute: 00)->format(format: 'd.m.Y H:i:s'),
            sort: 100,
            user_id: 1
        );
    }

    private function removeAgent(): bool
    {
        if ($this->agentExist()) {
            CAgent::RemoveAgent(
                name: SyncAgent::AGENT_NAME,
                module: $this->MODULE_ID,
                user_id: 1
            );
            return true;
        }

        return false;
    }

    private function agentExist(): bool
    {
        return (bool)(CAgent::GetList(
            arOrder: ['ASC'],
            arFilter: [
            'ACTIVE' => 'Y',
            'NAME' => SyncAgent::AGENT_NAME,
            'MODULE_ID' => $this->MODULE_ID,
        ]))->fetch();
    }
}
