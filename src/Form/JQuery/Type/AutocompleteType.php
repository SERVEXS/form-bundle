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

use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * @author Bilal Amarni <bilal.amarni@gmail.com>
 */
class AutocompleteType extends AbstractType
{
    public function __construct(private $type, private ?ManagerRegistry $registry = null)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'configs' => $options['configs'],
            'suggestions' => $options['suggestions'],
            'route_name' => $options['route_name'],
        ]);

        // Adds a custom block prefix
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getBlockPrefix() . 'AutocompleteType.php' . $view->vars['name'], $view->vars['block_prefixes']),
            0,
            'genemu_jqueryautocomplete'
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $type = $this->type;
        $registry = $this->registry;

        $resolver->setDefaults([
            'configs' => [],
            'suggestions' => [],
            'route_name' => null,
            'class' => null,
            'property' => null,
            'em' => null,
            'document_manager' => null,
        ]);

        $resolver->setNormalizer('em', function (Options $options, $manager) use ($registry, $type) {
            if (!in_array($type, ['entity', 'document'])) {
                return null;
            }
            if (null !== $options['document_manager'] && $manager) {
                throw new InvalidArgumentException('You cannot set both an "em" and "document_manager" option.');
            }

            $manager = $options['document_manager'] ?: $manager;

            if (null === $manager) {
                return $registry->getManagerForClass($options['class']);
            }

            return $registry->getManager($manager);
        });

        $resolver->setNormalizer('suggestions', function (Options $options, $suggestions) use ($type) {
            if (null !== $options['route_name']) {
                return [];
            }
            if (empty($suggestions)) {
                switch ($type) {
                    case 'entity':
                    case 'document':
                        $propertyPath = $options['property'] ? new PropertyPath($options['property']) : null;
                        $suggestions = [];
                        $objects = $options['em']->getRepository($options['class'])->findAll();
                        foreach ($objects as $object) {
                            if ($propertyPath) {
                                $suggestions[] = PropertyAccess::createPropertyAccessor()->getValue($object, $propertyPath);
                            } elseif (method_exists($object, '__toString')) {
                                $suggestions[] = (string) $object;
                            } else {
                                throw new RuntimeException(sprintf('Cannot cast object of type "%s" to string, please implement a __toString method or set the "property" option to the desired value.', $object::class));
                            }
                        }

                        break;
                }
            }

            return $suggestions;
        });
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'genemu_jqueryautocomplete_' . $this->type;
    }
}
