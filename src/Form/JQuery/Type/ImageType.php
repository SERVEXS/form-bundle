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

use Genemu\Bundle\FormBundle\Gd\File\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ImageType.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class ImageType extends AbstractType
{
    /**
     * Constructs.
     *
     * @param string $selected
     */
    public function __construct(private $selected, private readonly array $thumbnails, private array $filters)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $configs = $options['configs'];
        $data = $form->getViewData();

        if (!empty($data)) {
            if (!$data instanceof Image) {
                $data = new Image($form->getConfig()->getAttribute('rootDir') . 'ImageType.php/' . $data);
            }

            if ($data->hasThumbnail($this->selected)) {
                $thumbnail = $data->getThumbnail($this->selected);

                $view->vars['thumbnail'] = [
                    'file' => $configs['folder'] . '/' . $thumbnail->getFilename(),
                    'width' => $thumbnail->getWidth(),
                    'height' => $thumbnail->getHeight(),
                ];
            }

            $value = $configs['folder'] . '/' . $data->getFilename();

            $view->vars = array_replace($view->vars, [
                'value' => $value,
                'file' => $value,
                'width' => $data->getWidth(),
                'height' => $data->getHeight(),
            ]);
        }

        $view->vars['filters'] = $this->filters;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'configs' => [
                'fileExt' => '*.jpg;*.gif;*.png;*.jpeg',
                'fileDesc' => 'Web Image Files (.jpg, .gif, .png, .jpeg)',
            ],
        ]);
    }

    public function getParent()
    {
        return 'genemu_jqueryfile';
    }

    public function getBlockPrefix()
    {
        return 'genemu_jqueryimage';
    }
}
