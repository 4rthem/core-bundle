<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class TranslatorTemplateRenderer implements TemplateRendererInterface
{
    private string $domain = 'email';

    private string $contentKeyPattern = 'email.%s.content';

    private string $subjectKeyPattern = 'email.%s.subject';

    public function __construct(
        private Environment $twig,
        private TranslatorInterface $translator,
    )
    {
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getTemplateBodyContent(string $key, string $locale): string
    {
        $id = sprintf($this->contentKeyPattern, $key);
        $trans = $this->translator->trans($id, [], $this->domain, $locale);
        if ($trans === $id) {
            throw new \InvalidArgumentException(sprintf('Undefined email content translation "%s"', $id));
        }

        return $trans;
    }

    public function getTemplateSubjectContent(string $key, string $locale): string
    {
        $id = sprintf($this->subjectKeyPattern, $key);
        $trans = $this->translator->trans($id, [], $this->domain, $locale);
        if ($trans === $id) {
            throw new \InvalidArgumentException(sprintf('Undefined email subject translation "%s"', $id));
        }

        return $trans;
    }

    public function renderBody(string $key, string $locale, array $params = []): string
    {
        $template = $this->twig->createTemplate($this->getTemplateBodyContent($key, $locale));

        return $template->render($params);
    }

    public function renderSubject(string $key, string $locale, array $params = []): string
    {
        $template = $this->twig->createTemplate($this->getTemplateSubjectContent($key, $locale));

        return $template->render($params);
    }
}
