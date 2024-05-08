<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Core\Type;

use Locale;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ReCaptchaType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class ReCaptchaType extends AbstractType
{
    private readonly string $publicKey;

    public function __construct(
        private readonly EventSubscriberInterface $validator,
        $publicKey,
        private readonly string $serverUrl,
        private readonly array $options
    ) {
        if (empty($publicKey)) {
            throw new RuntimeException('The child node "public_key" at path "genenu_form.captcha" must be configured.');
        }
        $this->publicKey = $publicKey;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->validator->addOptions($options['validator']);

        $builder
            ->addEventSubscriber($this->validator)
            ->setAttribute('option_validator', $this->validator->getOptions())
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'public_key' => $this->publicKey,
            'server' => $this->serverUrl,
            'configs' => $options['configs'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $configs = array_merge([
            'lang' => Locale::getDefault(),
        ], $this->options);

        $resolver
            ->setDefaults([
                'configs' => [],
                'validator' => [],
                'error_bubbling' => false,
            ])
            ->setAllowedTypes('configs', 'array')
            ->setAllowedTypes('validator', 'array')
            ->setNormalizer(
                'configs',
                fn (Options $options, $value) => array_merge($configs, $value)
            )
        ;
    }

    public function getName(): string
    {
        return 'genemu_recaptcha';
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_recaptcha';
    }
}
