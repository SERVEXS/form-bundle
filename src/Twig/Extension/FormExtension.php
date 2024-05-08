<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Twig\Extension;

use Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * FormExtension extends Twig with form capabilities.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class FormExtension extends AbstractExtension
{
    public function __construct(
        /**
         * This property is public so that it can be accessed directly from compiled
         * templates without having to call a getter, which slightly decreases performance.
         */
        public FormRendererInterface $renderer
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('form_javascript', $this->renderJavascript(...), ['is_safe' => ['html']]),
            new TwigFunction('form_stylesheet', null, [
                'is_safe' => ['html'],
                'node_class' => SearchAndRenderBlockNode::class,
            ]),
        ];
    }

    /**
     * Render Function Form Javascript.
     *
     * @param bool $prototype
     */
    public function renderJavascript(FormView $view, $prototype = false): string
    {
        $block = $prototype ? 'javascript_prototype' : 'javascript';

        return $this->renderer->searchAndRenderBlock($view, $block);
    }

    public function getName(): string
    {
        return 'genemu.twig.extension.form';
    }
}
