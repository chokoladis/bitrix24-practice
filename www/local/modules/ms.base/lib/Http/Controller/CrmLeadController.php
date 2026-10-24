<?php

declare(strict_types=1);

namespace Ms\Base\Http\Controller;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Request;
use Bitrix\Main\Validation\ValidationService;
use Bitrix\Main\Web\Http\Response;
use Bitrix\Main\Web\Json;
use MS\Base\DTO\Crm\LeadCreateRequest;
use Ms\Base\Message\CreateLeadMessage;
use MS\Base\Service\CrmLeadService;

class CrmLeadController extends Controller
{
    private ValidationService $validation;
    private CrmLeadService $leadService;

    public function __construct(?Request $request = null)
    {
        parent::__construct($request);

        $this->validation = ServiceLocator::getInstance()->get('main.validation.service');
        $this->leadService = new CrmLeadService();
    }

    public function configureActions()
    {
        return [
            'create' => [
                'prefilters' => [
                    new Csrf(false),
                    new HttpMethod([HttpMethod::METHOD_POST])
                    // new ActionFilter\Authentication(),
                ],
            ],
        ];
    }

    public function createAction(\Bitrix\Main\Engine\JsonPayload $json)
    {
        $parsed = $json->getDataList();

        //todo защита от дублирования и множества запросов
        try {
            $data = new LeadCreateRequest(
                $parsed->get('name'),
                (int)$parsed->get('phone'),
                $parsed->get('email'),
                $parsed->get('utm_source'),
                $parsed->get('address'),
                $parsed->get('comment')
            );

            $result = $this->validation->validate($data);
            if (!$result->isSuccess()) {
                $this->addErrors($result->getErrors());
                return null;
            }

            $GLOBALS['messageBus']->dispatch(new CreateLeadMessage($data));
        } catch (\InvalidArgumentException $e) {
            $this->addError(new Error('Передан не корректный тип данных'));

            \Bitrix\Main\Diag\Debug::writeToFile("[Error] crmLead controller validation - " . print_r([$e->getMessage(), $e->getFile(), $e->getLine()], true), "messenger.log");

        } catch (\Throwable $e) {
            $this->addError(new Error('Произошла непредвиденная ошибка'));

            \Bitrix\Main\Diag\Debug::writeToFile("[Error] crmLead controller throw - " . print_r([$e->getMessage(), $e->getFile(), $e->getLine()], true), "messenger.log");
        }

        return null;
    }
}