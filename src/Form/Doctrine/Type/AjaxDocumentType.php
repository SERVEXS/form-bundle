<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\Doctrine\Type;

use Genemu\Bundle\FormBundle\Form\Doctrine\ChoiceList\AjaxEntityChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * AjaxDocumentType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class AjaxDocumentType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'document_manager' => null,
            'class' => null,
            'property' => null,
            'query_builder' => null,
            'choices' => null,
            'group_by' => null,
            'ajax' => false,
            'choice_list' => fn (Options $options, $previousValue) => new AjaxEntityChoiceList(
                $options['em'],
                $options['class'],
                $options['property'],
                $options['query_builder'],
                $options['choices'],
                $options['group_by'],
                $options['ajax']
            ),
        ]);
    }

    public function getParent(): ?string
    {
        return 'document';
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_ajaxdocument';
    }
}
