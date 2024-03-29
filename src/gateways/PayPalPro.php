<?php

namespace craft\commerce\paypal\gateways;

use Craft;
use craft\commerce\omnipay\base\CreditCardGateway;
use Omnipay\Common\AbstractGateway;
use Omnipay\Omnipay;
use Omnipay\PayPal\PayPalItemBag;
use Omnipay\PayPal\ProGateway as Gateway;

/**
 * PayPalPro represents PayPal Pro gateway
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since     1.0
 */
class PayPalPro extends CreditCardGateway
{
    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $signature;

    /**
     * @var string
     */
    public $testMode;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'PayPal Pro');
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('commerce-paypal/pro/gatewaySettings', ['gateway' => $this]);
    }

    /**
     * @inheritdoc
     */
    protected function createGateway(): AbstractGateway
    {
        /** @var Gateway $gateway */
        $gateway = static::createOmnipayGateway($this->getGatewayClassName());

        $gateway->setUsername(Craft::parseEnv($this->user));
        $gateway->setPassword(Craft::parseEnv($this->password));
        $gateway->setSignature(Craft::parseEnv($this->signature));
        $gateway->setTestMode($this->testMode);

        return $gateway;
    }

    /**
     * @inheritdoc
     */
    protected function getGatewayClassName()
    {
        return '\\'.Gateway::class;
    }

    protected function getItemBagClassName(): string
    {
        return PayPalItemBag::class;
    }
}
