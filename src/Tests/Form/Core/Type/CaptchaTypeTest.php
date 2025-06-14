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

use Genemu\Bundle\FormBundle\Form\Core\Type\CaptchaType;
use Genemu\Bundle\FormBundle\Tests\Form\Type\TypeTestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
class CaptchaTypeTest extends TypeTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!function_exists('gd_info')) {
            $this->markTestSkipped('Gd not installed');
        }
    }

    public function testDefaultConfigs()
    {
        $form = $this->factory->create(CaptchaType::class);
        $view = $form->createView();
        $captcha = $form->getConfig()->getAttribute('captcha');

        $this->assertEquals(100, $view->vars['width']);
        $this->assertEquals(30, $view->vars['height']);
        $this->assertStringStartsWith('data:image/png;base64,', $view->vars['src']);

        $this->assertEquals(4, $captcha->getLength());
    }

    public function testConfigs()
    {
        $form = $this->factory->create(CaptchaType::class, null, [
            'width' => 200,
            'font_color' => ['000'],
            'code' => '1111',
            'format' => 'gif',
        ]);

        $view = $form->createView();
        $captcha = $form->getConfig()->getAttribute('captcha');

        $this->assertEquals(200, $view->vars['width']);
        $this->assertEquals(md5('1111s$cr$t'), $captcha->getCode());
        $this->assertStringStartsWith('data:image/gif;base64,', $view->vars['src']);
        $this->assertEquals(4, $captcha->getLength());
    }

    public function testFaultFonts()
    {
        try {
            $form = $this->factory->create(CaptchaType::class, null, [
                'fonts' => ['toto.ttf'],
            ]);
        } catch (FileNotFoundException $excepted) {
            $this->assertStringStartsWith('The file', $excepted->getMessage());
            $this->assertStringEndsWith('does not exist', $excepted->getMessage());

            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testFaultFormat()
    {
        $form = $this->factory->create(CaptchaType::class, null, [
            'format' => 'bar',
        ]);

        $view = $form->createView();

        $this->assertStringStartsWith('data:image/jpeg;base64,', $view->vars['src']);
    }

    public function testCodePasses()
    {
        $form = $this->factory->create(CaptchaType::class);
        $form->createView();

        $form->submit('1234');

        $this->assertTrue($form->isValid());
    }

    public function testCodeFails()
    {
        $form = $this->factory->create(CaptchaType::class);
        $form->createView();

        $form->submit('4321');

        $this->assertFalse($form->isValid());
    }
}
