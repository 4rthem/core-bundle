<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

interface TemplateRendererInterface
{
    public function renderBody(string $key, string $locale, array $params = []): string;

    public function renderSubject(string $key, string $locale, array $params = []): string;
}
