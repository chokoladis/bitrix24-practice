<?

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;

$bitrixConfig = \Bitrix\Main\Config\Configuration::getInstance()->get('connections');
$dbSettings = $bitrixConfig['default'];

$connectionParams = [
    'dbname'   => $dbSettings['database'],
    'user'     => $dbSettings['login'],
    'password' => $dbSettings['password'],
    'host'     => $dbSettings['host'],
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8mb4',
];

$doctrineConnection = DriverManager::getConnection($connectionParams);

$dbTransport = new \Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport(
    new Connection([
        'table_name' => 'symfony_queue',
        'queue_name' => 'default_async',
        'auto_setup' => true,
    ], $doctrineConnection),
    new \Symfony\Component\Messenger\Transport\Serialization\PhpSerializer()
);

//todo rework construct
$handlersLocator = new \Symfony\Component\Messenger\Handler\HandlersLocator([
    \Ms\Base\Message\CreateLeadMessage::class => [new \Ms\Base\MessageHandler\CreateLeadHandler(new \Ms\Base\Service\CrmLeadService())]
]);

$miniContainer = new \Ms\Base\Tool\MiniContainer([
    'db_transport' => $dbTransport
]);

$sendersLocator = new \Symfony\Component\Messenger\Transport\Sender\SendersLocator([
    \Ms\Base\Message\CreateLeadMessage::class => ['db_transport'],
], $miniContainer);

$GLOBALS['messageBus'] = new \Symfony\Component\Messenger\MessageBus([
    new \Symfony\Component\Messenger\Middleware\SendMessageMiddleware($sendersLocator),
    new \Symfony\Component\Messenger\Middleware\HandleMessageMiddleware($handlersLocator),
]);


$GLOBALS['messengerDbTransport'] = $dbTransport;