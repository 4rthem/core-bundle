<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

class DebugTemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        private readonly TranslatorTemplateRenderer $translatorRenderer,
        private readonly TwigTemplateRenderer $twigRenderer,
        private readonly string $templateDir,
    ) {
    }

    public function renderBody(string $key, string $locale, array $params = []): string
    {
        $content = $this->translatorRenderer->getTemplateBodyContent($key, $locale);

        $template = $this->twigRenderer->getBodyTemplate($key, $locale);
        $this->writeTemplate($template, $content);

        return $this->twigRenderer->renderBody($key, $locale, $params);
    }

    public function renderSubject(string $key, string $locale, array $params = []): string
    {
        $content = $this->translatorRenderer->getTemplateSubjectContent($key, $locale);

        $template = $this->twigRenderer->getSubjectTemplate($key, $locale);
        $this->writeTemplate($template, $content);

        return $this->twigRenderer->renderSubject($key, $locale, $params);
    }

    private function writeTemplate(string $template, string $content): void
    {
        $dir = dirname($template);
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        file_put_contents($this->templateDir.'/'.$template, $content);
    }
}
