<?

namespace Ms\Base\DTO\Crm;

use Bitrix\Main\Validation\Rule\Email;
use Bitrix\Main\Validation\Rule\Length;
use Bitrix\Main\Validation\Rule\NotEmpty;
use Bitrix\Main\Validation\Rule\Phone;

final readonly class LeadCreateRequest {
    public function __construct(
        #[NotEmpty]
        #[Length(max: 120)]
        public string $name,
        #[NotEmpty]
        #[Phone]
        public string $phone,
        #[NotEmpty]
        #[Email]
        public string $email,
        // todo enum?
        #[Length(4)]
        public string $utm_source
    )
    {
    }
}