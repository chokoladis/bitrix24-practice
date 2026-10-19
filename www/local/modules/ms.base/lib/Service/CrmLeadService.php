<?

namespace Ms\Base\Service;

use Bitrix\Main\Web\HttpClient;
use MS\Base\DTO\Crm\LeadCreateRequest;

class CrmLeadService
{
    public function create(LeadCreateRequest $request)
    {
        $httpClient = new HttpClient();

        return $httpClient->post(sprintf('https://b24.local/rest/1/%s/crm.lead.add.json', $_ENV('WEBHOOK_TOKEN_CRM_LEAD_CREATE')), [
            'fields' => [
                'NAME' => $request->name,
                'PHONE' => [
                    "VALUE"=> $request->phone,
                    "VALUE_TYPE" => "WORK"
                ],                    
                'EMAIL' => $request->email,
                'SOURCE_ID' => 'OTHER',
                'SOURCE_DESCRIPTION' => $request->utm_source,
            ]
        ]);
    }
}