# Arthem Core Bundle

Shared building blocks for Symfony applications: a templated mailer, a session-aware Monolog
processor and a few validation constraints.

Supports Symfony 6.4, 7 and 8 on PHP 8.1+.

## Installation

```bash
composer require arthem/core-bundle
```

Then register the bundle in `config/bundles.php`:

```php
Arthem\Bundle\CoreBundle\ArthemCoreBundle::class => ['all' => true],
```

## Configuration

```yaml
# config/packages/arthem_core.yaml
arthem_core:
    mailer:
        sender_address: '%env(MAIL_SENDER_EMAIL)%'
        sender_name: '%env(MAIL_SENDER_NAME)%'
        translation_mailer: false
```

The mailer is disabled until `sender_address` is configured. It needs `symfony/mailer` and
`symfony/twig-bundle`.

## Mailer

`Arthem\Bundle\CoreBundle\Mailer\MailerInterface` renders a Twig template and sends it through the
Symfony mailer. A template extends `@ArthemCore/Mail/base.html.twig` and defines three blocks:

```twig
{% extends '@ArthemCore/Mail/base.html.twig' %}

{% block subject %}Welcome {{ user.name }}{% endblock %}
{% block content_text %}Hello {{ user.name }}{% endblock %}
{% block content_html %}<p>Hello {{ user.name }}</p>{% endblock %}
```

```php
$mailer->send('emails/welcome.html.twig', 'alice@example.com', ['user' => $user]);
$mailer->sendToUser('emails/welcome.html.twig', [], $user); // $user implements MailerUserInterface
```

Recipients and senders accept a single address, a list of addresses or an `email => name` map. When
the user implements `MailerLocaleUserInterface`, the email is rendered in that locale.

Services implementing `MessageProcessorInterface` are tagged automatically and receive every
`Email` before it is sent. Override `@ArthemCore/Mail/signature.html.twig` and
`@ArthemCore/Mail/signature.txt.twig` in your application to customize the signature.

The `arthem:mailer:send <email> <template>` command sends a template to a given address, which is
handy to check a transport.

## Logger

`SessionRequestProcessor` is registered as a Monolog processor and adds a `token` extra to every
record, derived from the current session id, so the lines of one session can be correlated.

## Validation constraints

| Constraint  | Target   | Purpose                                                                                   |
| ----------- | -------- | ----------------------------------------------------------------------------------------- |
| `Name`      | property | Letters, digits, spaces and a configurable set of special characters (`-'’` by default). |
| `Age`       | property | A birth date between `minAge` (18) and `maxAge` (130) years ago.                          |
| `DateRange` | class    | `startDate` must not be after `endDate` (property paths, configurable).                   |
| `Validate`  | property | Cascades validation to the nested object, mapping the current group to nested groups.     |

```php
#[DateRange(startDate: 'checkIn', endDate: 'checkOut')]
final class Booking
{
    #[Name]
    public ?string $guestName = null;

    #[Age(minAge: 21)]
    public ?\DateTimeImmutable $birthDate = null;

    #[Validate(map: ['Default' => ['payment']])]
    public ?PaymentCard $card = null;
}
```

Messages are translated in the `validators` domain (English and French included).

## Development

```bash
composer install
composer test          # php-cs-fixer (dry run), phpstan, phpunit
```
