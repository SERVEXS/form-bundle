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

use Genemu\Bundle\FormBundle\Form\Core\Validator\CaptchaValidator;
use Genemu\Bundle\FormBundle\Gd\Type\Captcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CaptchaType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class CaptchaType extends AbstractType
{
    public function __construct(
        private readonly Captcha $captcha,
        private readonly array $options
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->captcha->setOptions($options);

        $builder
            ->addEventSubscriber(new CaptchaValidator($this->captcha))
            ->setAttribute('captcha', $this->captcha)
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $captcha = $this->captcha;

        $view->vars = array_replace($view->vars, [
            'value' => '',
            'src' => $captcha->getBase64($options['format']),
            'width' => $captcha->getWidth(),
            'height' => $captcha->getHeight(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $defaults = array_merge(
            ['attr' => ['autocomplete' => 'off']],
            $this->options
        );

        $resolver->setDefaults($defaults);
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function getName(): string
    {
        return 'genemu_captcha';
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_captcha';
    }
}
