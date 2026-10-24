<?
declare(strict_types=1);

namespace Ms\Base\DTO\Crm;

use Bitrix\Main\Validation\Rule\Email;
use Bitrix\Main\Validation\Rule\Length;
use Bitrix\Main\Validation\Rule\NotEmpty;
use Bitrix\Main\Validation\Rule\Phone;

// todo проверку по длинне и кирилице
final readonly class LeadCreateRequest {
    public function __construct(
        #[NotEmpty]
        #[Length(max: 120)]
        public string $name,
        #[NotEmpty]
        #[Phone]
        public int $phone,
        #[NotEmpty]
        #[Email]
        public string $email,
        // todo enum?
        #[Length(4)]
        public string $utm_source,
        public ?string $address = null,
        public ?string $comment = null
    )
    {
    }
}