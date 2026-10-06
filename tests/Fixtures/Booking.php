<?php

namespace Arthem\Bundle\CoreBundle\Tests\Fixtures;

use Arthem\Bundle\CoreBundle\Validator\Constraints\Age;
use Arthem\Bundle\CoreBundle\Validator\Constraints\DateRange;
use Arthem\Bundle\CoreBundle\Validator\Constraints\Name;
use Arthem\Bundle\CoreBundle\Validator\Constraints\Validate;

#[DateRange(startDate: 'checkIn', endDate: 'checkOut')]
final class Booking
{
    #[Name]
    public ?string $guestName = null;

    #[Age(minAge: 21)]
    public ?\DateTimeImmutable $birthDate = null;

    public ?\DateTimeImmutable $checkIn = null;

    public ?\DateTimeImmutable $checkOut = null;

    #[Validate(map: ['Default' => ['payment']])]
    public ?PaymentCard $card = null;
}
