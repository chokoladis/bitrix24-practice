<?

declare(strict_types=1);

namespace Ms\Base\Message;

use Ms\Base\DTO\Crm\LeadCreateRequest;

final readonly class CreateLeadMessage {
    public function __construct(
        public LeadCreateRequest $data
    )
    {}
}