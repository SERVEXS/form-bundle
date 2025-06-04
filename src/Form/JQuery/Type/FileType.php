<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Form\JQuery\Type;

use Genemu\Bundle\FormBundle\Form\Core\EventListener\FileListener;
use Genemu\Bundle\FormBundle\Form\JQuery\DataTransformer\FileToValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * FileType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 * @author Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class FileType extends AbstractType
{
    public function __construct(private array $options, private readonly string $rootDir)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $configs = $options['configs'];

        $builder
            ->addEventSubscriber(new FileListener($this->rootDir, $options['multiple']))
            ->addViewTransformer(new FileToValueTransformer($this->rootDir, $configs['folder'], $options['multiple']))
            ->setAttribute('rootDir', $this->rootDir);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'type' => 'hidden',
            'value' => $form->getViewData(),
            'multiple' => $options['multiple'],
            'configs' => $options['configs'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $configs = $this->options;

        $resolver
            ->setDefaults([
                'data_class' => null,
                'required' => false,
                'multiple' => false,
                'configs' => [],
            ])
            ->setNormalizer('configs', function (Options $options, $value) use ($configs) {
                if (!$options['multiple']) {
                    $value['multi'] = false;
                }

                return array_merge($configs, $value);
            });
    }

    public function getParent(): ?string
    {
        return \Symfony\Component\Form\Extension\Core\Type\FileType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jqueryfile';
    }
}
