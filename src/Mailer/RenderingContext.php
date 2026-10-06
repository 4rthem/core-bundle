<?php

declare(strict_types=1);

namespace Arthem\Bundle\CoreBundle\Mailer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RenderingContext
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly UrlGeneratorInterface $router,
    ) {
    }

    public function getLocale(): string
    {
        return $this->translator instanceof LocaleAwareInterface ? $this->translator->getLocale() : \Locale::getDefault();
    }

    public function setLocale(string $locale): void
    {
        \Locale::setDefault($locale);
        if ($this->translator instanceof LocaleAwareInterface) {
            $this->translator->setLocale($locale);
        }
        $this->router->getContext()->setParameter('_locale', $locale);
    }
}
