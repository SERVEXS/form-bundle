<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('genenu_form');
        $rootNode = $treeBuilder->getRootNode();

        $this->addCaptcha($rootNode);
        $this->addRecaptcha($rootNode);
        $this->addTinymce($rootNode);
        $this->addDate($rootNode);
        $this->addFile($rootNode);
        $this->addImage($rootNode);
        $this->addAutocompleter($rootNode);
        $this->addTokeninput($rootNode);
        $this->addAutocomplete($rootNode);
        $this->addSelect2($rootNode);

        return $treeBuilder;
    }

    /**
     * Add Configuration Captcha.
     */
    private function addCaptcha(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('captcha')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('driver')->defaultValue('gd')->end()
                        ->scalarNode('width')->defaultValue(100)->end()
                        ->scalarNode('height')->defaultValue(30)->end()
                        ->scalarNode('length')->defaultValue(4)->end()
                        ->scalarNode('format')->defaultValue('png')->end()
                        ->arrayNode('chars')
                            ->defaultValue(range(0, 9))
                            ->beforeNormalization()
                                ->ifTrue(fn ($v) => !is_array($v))
                                ->then(fn ($v) => str_split((string) $v))
                            ->end()
                            ->beforeNormalization()
                                ->always()
                                ->then(fn ($v) => array_filter($v, fn ($v) => ' ' !== $v && $v))
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('font_size')->defaultValue(18)->end()
                        ->booleanNode('grayscale')->defaultFalse()->end()
                        ->arrayNode('font_color')
                            ->defaultValue(['252525', '8B8787', '550707', '3526E6', '88531E'])
                            ->beforeNormalization()
                                ->always()
                                ->then(fn($v) => array_filter($v, function ($v) {
                                    $v = preg_replace('/[^0-9A-Fa-f]/', '', (string) $v);

                                    return in_array(strlen($v), [3, 6]);
                                }))
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('font_dir')
                            ->defaultValue('%kernel.project_dir%/public/bundles/genemuform/fonts')
                        ->end()
                        ->arrayNode('fonts')
                            ->defaultValue(
                                ['akbar.ttf', 'brushcut.ttf', 'molten.ttf', 'planetbe.ttf', 'whoobub.ttf']
                            )
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('background_color')->defaultValue('DDDDDD')->end()
                        ->scalarNode('border_color')->defaultValue('000000')->end()
                        ->scalarNode('code')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration Recaptcha.
     */
    private function addRecaptcha(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('recaptcha')
                    ->canBeUnset()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('public_key')->isRequired()->end()
                        ->scalarNode('private_key')->isRequired()->end()
                        ->arrayNode('validation')
                            ->canBeUnset()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('host')->defaultValue('www.google.com')->end()
                                ->scalarNode('port')->defaultValue(80)->end()
                                ->scalarNode('path')->defaultValue('/recaptcha/api/verify')->end()
                                ->scalarNode('timeout')->defaultValue(10)->end()
                                ->scalarNode('code')->defaultNull()->end()
                                ->arrayNode('proxy')
                                    ->canBeUnset()
                                    ->children()
                                        ->scalarNode('host')->isRequired()->end()
                                        ->scalarNode('port')->defaultValue('80')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->variableNode('configs')->defaultValue([])->end()
                    /* TO BE DEPRECATED */
                        ->scalarNode('code')->defaultNull()->end()
                        ->scalarNode('server_url')->defaultValue('http://www.google.com/recaptcha/api')->end()
                        ->arrayNode('ssl')
                            ->canBeUnset()
                            ->treatNullLike(['use' => true])
                            ->treatTrueLike(['use' => true])
                            ->children()
                                ->booleanNode('use')->defaultTrue()->end()
                                ->scalarNode('server_url')
                                    ->defaultValue('https://www.google.com/recaptcha/api')
                                ->end()
                            ->end()
                        ->end()
                    /* END */
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration Tinymce.
     */
    private function addTinymce(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('tinymce')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('theme')->defaultValue('modern')->end()
                        ->scalarNode('script_url')->end()
                        ->variableNode('configs')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration Date.
     */
    private function addDate(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('date')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->variableNode('configs')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration File.
     */
    private function addFile(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('file')
                    ->canBeUnset()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('swf')->isRequired()->end()
                        ->scalarNode('cancel_img')->defaultValue('/bundles/genemuform/images/cancel.png')->end()
                        ->scalarNode('folder')->defaultValue('/upload')->end()
                        ->variableNode('configs')->defaultValue([])->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration Image.
     */
    private function addImage(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('image')
                    ->canBeUnset()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('selected')->defaultValue('large')->end()
                        ->arrayNode('filters')
                            ->defaultValue(['rotate', 'bw', 'negative', 'sepia', 'crop'])
                            ->prototype('scalar')->end()
                        ->end()
                        ->variableNode('thumbnails')
                            ->defaultValue([
                                'small' => [100, 100],
                                'medium' => [200, 200],
                                'large' => [500, 500],
                                'extra' => [1024, 768],
                            ])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration Tokeninput.
     */
    private function addTokeninput(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('tokeninput')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('doctrine')->defaultTrue()->end()
                        ->booleanNode('mongodb')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration Autocompleter.
     */
    private function addAutocompleter(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('autocompleter')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('doctrine')->defaultTrue()->end()
                        ->booleanNode('mongodb')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Add configuration Autocompleter.
     */
    private function addAutocomplete(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('autocomplete')
                    ->canBeUnset()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->booleanNode('doctrine')->defaultTrue()->end()
                        ->booleanNode('mongodb')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addSelect2(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('select2')
                    ->canBeUnset()
                    ->treatNullLike(['enabled' => true])
                    ->treatTrueLike(['enabled' => true])
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->arrayNode('configs')
                            ->prototype('variable')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
