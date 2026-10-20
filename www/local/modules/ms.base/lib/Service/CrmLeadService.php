<?

namespace Ms\Base\Service;

use Bitrix\Main\Web\HttpClient;
use MS\Base\DTO\Crm\LeadCreateRequest;

class CrmLeadService
{
    public function checkExistsContactAndCreateLead(LeadCreateRequest $request) {
        $httpClient = new HttpClient();

        return $httpClient->post(sprintf('https://b24.local/rest/1/%s/batch.json', $_ENV['WEBHOOK_TOKEN_BATCH_CRM']), [
            'halt' => 0,
            'cmd' => [
                'dup' => 'crm.duplicate.findbycomm?' . http_build_query([
                    'entity_type' => 'CONTACT',
                    'type'        => 'EMAIL',
                    'values'      => [$request->email],
                ]),
                'lead' => 'crm.lead.add?' . http_build_query([
                    'fields' => [
                        'NAME' => $request->name,
                        'PHONE' => [
                            ["VALUE"=> $request->phone,
                            "VALUE_TYPE" => "WORK"]
                        ],                    
                        'EMAIL' => [
                        [  'VALUE' => $request->email,
                            'VALUE_TYPE' => 'WORK']
                        ],
                        'SOURCE_ID' => 'OTHER',
                        'SOURCE_DESCRIPTION' => $request->utm_source,
                        'ADDRESS' => $request->address,
                        'COMMENTS' => $request->comment,
                        'CONTACT_ID' => '$result[dup][CONTACT_ID][0]'
                    ]
                ])
            ]
        ]);
    }
}