<?php

namespace G\TranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GTranslationBundle extends Bundle
{

	 /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'LexikTranslationBundle';
    }
}
