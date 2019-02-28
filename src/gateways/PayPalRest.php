<?php

namespace craft\commerce\paypal\gateways;

use Craft;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\omnipay\base\CreditCardGateway;
use craft\commerce\paypal\models\PayPalRestPaymentForm;
use craft\helpers\StringHelper;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;
use Omnipay\PayPal\PayPalItemBag;
use Omnipay\PayPal\RestGateway as Gateway;

/**
 * PayPalRest represents the PayPal Rest gateway
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since     1.0
 */
class PayPalRest extends CreditCardGateway
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $clientId;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var string
     */
    public $testMode;

    // Public Methods
    // =========================================================================


    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'PayPal REST gateway');
    }

    /**
     * @inheritdoc
     */
    public function getPaymentFormModel(): BasePaymentForm
    {
        return new PayPalRestPaymentForm();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('commerce-paypal/rest/gatewaySettings', ['gateway' => $this]);
    }

    /**
     * @inheritdoc
     */
    public function populateRequest(array &$request, BasePaymentForm $paymentForm = null)
    {
        parent::populateRequest($request, $paymentForm);

        if ($paymentForm && $paymentForm->hasProperty('cardReference') && $paymentForm->cardReference) {
            $request['cardReference'] = $paymentForm->cardReference;
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createGateway(): AbstractGateway
    {
        /** @var Gateway $gateway */
        $gateway = static::createOmnipayGateway($this->getGatewayClassName());

        $gateway->setClientId($this->clientId);
        $gateway->setSecret($this->secret);
        $gateway->setTestMode($this->testMode);

        return $gateway;
    }

    /**
     * @inheritdoc
     */
    protected function extractPaymentSourceDescription(ResponseInterface $response): string
    {
        $data = $response->getData();
        return Craft::t('commerce-paypal', '{cardType} ending in {last4}', ['cardType' => StringHelper::upperCaseFirst($data['type']), 'last4' => $data['number']]);
    }

    /**
     * @inheritdoc
     */
    protected function getGatewayClassName()
    {
        return '\\'.Gateway::class;
    }

    /**
     * @inheritdoc
     */
    protected function getItemBagClassName(): string
    {
        return PayPalItemBag::class;
    }
}
