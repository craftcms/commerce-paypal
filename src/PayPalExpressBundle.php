<?php
namespace craft\commerce\paypal;

use craft\web\AssetBundle;

/**
 * Asset bundle for the PayPal REST payment
 */
class PayPalExpressBundle extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@craft/commerce/paypal/resources';

        $this->js = [
            'js/paymentForm.js',
        ];

        parent::init();
    }
}
