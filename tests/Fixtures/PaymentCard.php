<?php

namespace Arthem\Bundle\CoreBundle\Tests\Fixtures;

use Symfony\Component\Validator\Constraints\NotBlank;

final class PaymentCard
{
    #[NotBlank(groups: ['payment'])]
    public ?string $holder = null;
}
