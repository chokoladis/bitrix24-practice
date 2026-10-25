<?

declare(strict_types=1);

namespace Ms\Base\MessageHandler;

use Ms\Base\Message\CreateLeadMessage;
use Ms\Base\Service\CrmLeadService;

class CreateLeadHandler {

    public function __construct(
        private CrmLeadService $leadService
    ){
    }

    public function __invoke(CreateLeadMessage $message)
    {
        try {
            $response = json_decode($this->leadService->checkExistsContactAndCreateLead($message->data), true);

            if (!$response['result']['result']['lead']) {
                \Bitrix\Main\Diag\Debug::writeToFile(date('m.d h:i:s'). "[Error] createLeadHandler - " . print_r($response, true), "messenger.log");

                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $e){
            \Bitrix\Main\Diag\Debug::writeToFile(date('m.d h:i:s'). "[Throw] createLeadHandler - " . print_r([$e->getMessage(), $e->getLine(), $e->getFile()], true), "errors.log");
        }
    }
}