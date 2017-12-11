<?php

namespace craft\commerce\paypal\models;

use craft\commerce\models\payments\CreditCardPaymentForm;
use craft\commerce\models\PaymentSource;

/**
 * PayPal REST payment form model.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  1.0
 */
class PayPalRestPaymentForm extends CreditCardPaymentForm
{
    /**
     * @var string credit card reference
     */
    public $cardReference;

    /**
     * @inheritdoc
     */
    public function populateFromPaymentSource(PaymentSource $paymentSource)
    {
        $this->cardReference = $paymentSource->token;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        if (empty($this->cardReference)) {
            return parent::rules();
        }

        return [];
    }
}
