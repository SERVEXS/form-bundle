<?php

/*
 * This file is part of the GenemuFormBundle package.
 *
 * (c) Olivier Chauvel <olivier@generation-multiple.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Genemu\Bundle\FormBundle\Controller;

use Genemu\Bundle\FormBundle\Gd\Type\Captcha;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Base64Controller
 *
 * @author Olivier Chauvel <olivier@generation-multiple.com>
 */
readonly class Base64Controller
{
    public function __construct(private Captcha $captcha)
    {
    }

    public function refreshCaptchaAction(Request $request)
    {
        $captcha = $this->captcha;
        $options = $request->getSession()->get('genemu_form.captcha.options', []);
        $captcha->setOptions($options);
        $datas = preg_split('([;,]{1})', substr($captcha->getBase64(), 5));

        return new Response(base64_decode($datas[2]), 200, ['Content-Type' => $datas[0]]);
    }

    public function base64Action(Request $request)
    {
        $query = $request->server->get('QUERY_STRING');
        $datas = preg_split('([;,]{1})', $query);

        return new Response(base64_decode($datas[2]), 200, ['Content-Type' => $datas[0]]);
    }
}
