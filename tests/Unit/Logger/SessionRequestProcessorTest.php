<?php

namespace Arthem\Bundle\CoreBundle\Tests\Unit\Logger;

use Arthem\Bundle\CoreBundle\Logger\SessionRequestProcessor;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class SessionRequestProcessorTest extends TestCase
{
    public function testRecordIsUntouchedWithoutRequest(): void
    {
        $record = $this->record();

        self::assertSame([], (new SessionRequestProcessor(new RequestStack()))->processRecord($record)->extra);
    }

    public function testRecordIsUntouchedWhenSessionIsNotStarted(): void
    {
        $requestStack = $this->requestStackWithSession(new Session(new MockArraySessionStorage()));

        self::assertSame([], (new SessionRequestProcessor($requestStack))->processRecord($this->record())->extra);
    }

    public function testTokenIsDerivedFromTheSessionId(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $requestStack = $this->requestStackWithSession($session);

        $record = (new SessionRequestProcessor($requestStack))->processRecord($this->record());

        self::assertMatchesRegularExpression('/^'.preg_quote(substr($session->getId(), 0, 8), '/').'-.{8}$/', $record->extra['token']);
    }

    private function requestStackWithSession(Session $session): RequestStack
    {
        $request = Request::create('/');
        $request->setSession($session);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        return $requestStack;
    }

    private function record(): LogRecord
    {
        return new LogRecord(new \DateTimeImmutable(), 'app', Level::Info, 'Tea party started');
    }
}
