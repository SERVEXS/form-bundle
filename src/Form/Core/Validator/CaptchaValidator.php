<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Form\Core\Validator;

use Genemu\Bundle\FormBundle\Gd\Type\Captcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * CaptchaValidator.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
readonly class CaptchaValidator implements EventSubscriberInterface
{
    public function __construct(private Captcha $captcha)
    {
    }

    public function validate(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (
            $this->captcha->getLength() !== strlen($data)
            || $this->captcha->getCode() !== $this->captcha->encode($data)
        ) {
            $form->addError(new FormError('The captcha is invalid.'));
        }

        $this->captcha->removeCode();
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::POST_SUBMIT => 'validate'];
    }
}
