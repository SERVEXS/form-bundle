<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Tests\Form\Core\Type;

use Genemu\Bundle\FormBundle\Form\Core\Type\ReCaptchaType;
use Genemu\Bundle\FormBundle\Tests\Form\Type\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class ReCaptchaTypeTest extends TypeTestCase
{
    public function testDefaultConfigs()
    {
        $form = $this->factory->create(ReCaptchaType::class);
        $view = $form->createView();

        $this->assertEquals('publicKey', $view->vars['public_key']);
        $this->assertEquals('http://www.google.com/recaptcha/api', $view->vars['server']);
        $this->assertEquals(['lang' => 'en'], $view->vars['configs']);

        $this->assertEquals([
            'host' => 'www.google.com',
            'port' => 80,
            'path' => '/recaptcha/api/verify',
            'timeout' => 10,
            'code' => '1234',
        ], $form->getConfig()->getAttribute('option_validator'));
    }

    public function testConfigs()
    {
        $form = $this->factory->create(ReCaptchaType::class, null, [
            'configs' => [
                'theme' => 'blackglass',
            ],
            'validator' => ['timeout' => 30],
        ]);
        $view = $form->createView();

        $this->assertEquals('publicKey', $view->vars['public_key']);
        $this->assertEquals(['theme' => 'blackglass', 'lang' => 'en'], $view->vars['configs']);

        $this->assertEquals([
            'host' => 'www.google.com',
            'port' => 80,
            'path' => '/recaptcha/api/verify',
            'timeout' => 30,
            'code' => '1234',
        ], $form->getConfig()->getAttribute('option_validator'));
    }

    /**
     * @dataProvider provideCodes
     */
    public function testCode($code, $isValid)
    {
        $request = new Request([], ['recaptcha_response_field' => $code]);
        $this->requestStack->method('getMasterRequest')->willReturn($request);

        $form = $this->factory->create(ReCaptchaType::class);

        $form->submit(null);

        $this->assertEquals($isValid, $form->isValid());
    }

    public function provideCodes()
    {
        return [
            ['1234', true],
            ['4321', false],
        ];
    }
}
