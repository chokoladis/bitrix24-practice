<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Routing\RoutingConfigurator;

Loader::includeModule('ms.base');

return function (RoutingConfigurator $routes) {
    // создано для демонстрации, с пониманием как работают вебхуки б24. Подразумевается наличие токена авторизации
    $routes->post('/api/v1/crm/lead/create', [Ms\Base\Http\Controller\CrmLeadController::class, 'create']);
};