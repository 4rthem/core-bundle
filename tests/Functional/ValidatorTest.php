<?php

namespace Arthem\Bundle\CoreBundle\Tests\Functional;

use Arthem\Bundle\CoreBundle\Tests\Fixtures\Booking;
use Arthem\Bundle\CoreBundle\Tests\Fixtures\PaymentCard;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorTest extends KernelTestCase
{
    public function testValidBookingHasNoViolation(): void
    {
        $booking = new Booking();
        $booking->guestName = "Alice O'Connor-Liddell";
        $booking->birthDate = new \DateTimeImmutable('-30 years');
        $booking->checkIn = new \DateTimeImmutable('2026-10-10');
        $booking->checkOut = new \DateTimeImmutable('2026-10-12');
        $booking->card = new PaymentCard();
        $booking->card->holder = 'Alice';

        self::assertCount(0, $this->validator()->validate($booking));
    }

    public function testConstraintsReportTranslatedViolations(): void
    {
        $booking = new Booking();
        $booking->guestName = 'R2-D2 <3';
        $booking->birthDate = new \DateTimeImmutable('-18 years');
        $booking->checkIn = new \DateTimeImmutable('2026-10-12');
        $booking->checkOut = new \DateTimeImmutable('2026-10-10');
        $booking->card = new PaymentCard();

        $violations = $this->validator()->validate($booking);

        self::assertSame([
            'birthDate' => 'You must be over 21',
            'card.holder' => 'This value should not be blank.',
            'checkOut' => 'Invalid date range',
            'guestName' => 'Invalid name',
        ], $this->messagesByPath($violations));
    }

    public function testViolationsAreTranslatedInFrench(): void
    {
        $booking = new Booking();
        $booking->guestName = 'R2-D2 <3';

        self::getContainer()->get('translator')->setLocale('fr');
        $violations = $this->validator()->validate($booking);

        self::assertSame(['guestName' => "Ce n'est pas un nom correct"], $this->messagesByPath($violations));
    }

    private function validator(): ValidatorInterface
    {
        return self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * @return array<string, string>
     */
    private function messagesByPath(ConstraintViolationListInterface $violations): array
    {
        $messages = [];
        foreach ($violations as $violation) {
            $messages[$violation->getPropertyPath()] = (string) $violation->getMessage();
        }
        ksort($messages);

        return $messages;
    }
}
