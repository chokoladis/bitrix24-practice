<?php

namespace Ms\Base\Http\Controller;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Request;
use Bitrix\Main\Validation\ValidationService;
use MS\Base\DTO\Crm\LeadCreateRequest;
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

        $data = new LeadCreateRequest(
            $parsed->get('name'),
            $parsed->get('phone'),
            $parsed->get('email'),
            $parsed->get('utm_source')
        );

        $result = $this->validation->validate($data);
        
        if (!$result->isSuccess())
        {
            $this->addErrors($result->getErrors());
            return null;
        }

        $response = json_decode($this->leadService->create($data), true);

        return $response['result'] ?? '';
    }
}