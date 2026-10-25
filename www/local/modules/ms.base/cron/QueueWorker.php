<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../..');

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_NO_ACCELERATOR_RESET', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Symfony\Component\Messenger\Worker;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

\Bitrix\Main\Loader::includeModule('ms.base');

echo "[*] Воркер Symfony Messenger запущен на базе встроенной БД Битрикса...\n";

$handlersLocator = new HandlersLocator([
    \Ms\Base\Message\CreateLeadMessage::class => [new \Ms\Base\MessageHandler\CreateLeadHandler(new \Ms\Base\Service\CrmLeadService())],
]);
$workerBus = new MessageBus([new HandleMessageMiddleware($handlersLocator)]);

$transport = $GLOBALS['messengerDbTransport'];
$worker = new Worker(['db_transport' => $GLOBALS['messengerDbTransport']],
    $GLOBALS['messageBus']
);

$worker->run();