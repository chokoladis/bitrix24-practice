<?php

namespace Ms\Tests;

use Bitrix\Main\Type\ParameterDictionary;
use Ms\Base\DTO\Crm\LeadCreateRequest;
use Ms\Base\Http\Controller\CrmLeadController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CrmLeadControllerTest extends TestCase
{
    #[Test]
    public  function validationCreateSuccess()
    {
        $data = [
            'name' => 'test abgd',
            'phone' => 89321232434,
            'email' => 'sallerShip@mail.ru',
            'utm_source' => 'yandex',
            'address' => 'moscow',
            'comment' => 'Побыстрее привезите воду'
        ];

        $payload = new ParameterDictionary($data);

        $controller = new CrmLeadController();
        $response = $controller->validationCreate($payload);

        self::assertInstanceOf(LeadCreateRequest::class, $response);
    }

    #[Test]
    public  function validationCreateThrowTypeError()
    {
        $data = [
            'name' => 'test user',
            'phone' => '89321232434',
            'email' => 'moscow@mail.ru',
            'utm_source' => 22,
            'address' => true,
        ];

        $payload = new ParameterDictionary($data);


        $this->expectException(\TypeError::class);

        $controller = new CrmLeadController();
        $controller->validationCreate($payload);
    }

    #[Test]
    public  function validationCreateReturnBadRequest()
    {
        $data = [
            'name' => 'test asdf',
            'phone' => '123456',
            'email' => 'moscow',
            'utm_source' => 'google',
        ];

        $payload = new ParameterDictionary($data);

        $controller = new CrmLeadController();
        $controller->validationCreate($payload);

        self::assertNotEmpty($controller->getErrors());
    }
}