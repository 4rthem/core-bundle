<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigTemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        private Environment $twig,
        private string $templateDir,
    )
    {
        $loader = $this->twig->getLoader();
        if ($loader instanceof FilesystemLoader) {
            if (!is_dir($this->templateDir)) {
                mkdir($this->templateDir);
            }

            $loader->addPath($this->templateDir);
        }
    }

    public function renderBody(string $key, string $locale, array $params = []): string
    {
        return $this->twig->render($this->getBodyTemplate($key, $locale), $params);
    }

    public function renderSubject(string $key, string $locale, array $params = []): string
    {
        return $this->twig->render($this->getSubjectTemplate($key, $locale), $params);
    }

    public function getBodyTemplate(string $key, string $locale): string
    {
        return sprintf('content.%s.%s.html.twig', $key, $locale);
    }

    public function getSubjectTemplate(string $key, string $locale): string
    {
        return sprintf('subject.%s.%s.html.twig', $key, $locale);
    }
}
