<?

use Bitrix\Main\Application;
use Bitrix\Main\Data\Connection as DataConnection;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Mysite\Events\CRM\Deals;
use \Bitrix\Main\EventManager;
use Mysite\Entities\BonusesTable;

class ms_base extends CModule
{
    var $MODULE_ID = "ms.base";
    var $MODULE_NAME;
    var $MODULE_VERSION;

    private Connection|DataConnection $connection;

    function __construct()
    {
        /** @var array $arModuleVersion */

        include_once (__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

        $this->MODULE_NAME = 'Кастомный модуль';

        $this->connection = Application::getConnection();
    }

    function DoInstall()
    {
        if (!$GLOBALS['USER']->IsAdmin()) {
            return;
        }

        global $APPLICATION;

        RegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile('Установка модуля ' . $this->MODULE_ID, __DIR__ . '/step.php');
    }

    function DoUninstall()
    {
        global $APPLICATION;

        unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile( 'Удаление модуля ' . $this->MODULE_ID, __DIR__ . " /unstep.php");
    }
}