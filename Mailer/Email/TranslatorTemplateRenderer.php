<?php

namespace Arthem\Bundle\CoreBundle\Mailer\Email;

use Symfony\Component\Translation\TranslatorInterface;

class TranslatorTemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $domain = 'email';

    /**
     * @var string
     */
    private $contentKeyPattern = 'email.%s.content';

    /**
     * @var string
     */
    private $subjectKeyPattern = 'email.%s.subject';

    public function __construct(\Twig_Environment $twig, TranslatorInterface $translator)
    {
        $this->twig = $twig;
        $this->translator = $translator;
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
