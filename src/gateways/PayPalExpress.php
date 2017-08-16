<?php

namespace craft\commerce\paypal\gateways;

use Craft;
use craft\commerce\omnipay\base\OffsiteGatewayTrait;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\Transaction;
use craft\commerce\omnipay\base\Gateway as OmnipayGateway;
use craft\commerce\records\Transaction as TransactionRecord;
use Omnipay\Common\AbstractGateway;
use Omnipay\Omnipay;
use Omnipay\PayPal\PayPalItemBag;
use Omnipay\PayPal\ExpressGateway as Gateway;

/**
 * Stripe represents the Stripe gateway
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since     1.0
 */
class PayPalExpress extends OmnipayGateway
{

    use OffsiteGatewayTrait;

    // Properties
    // =========================================================================

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
     * @var string
     */
    public $solutionType;

    /**
     * @var string
     */
    public $landingPage;

    /**
     * @var string
     */
    public $brandName;

    /**
     * @var string
     */
    public $headerImageUrl;

    /**
     * @var string
     */
    public $logoImageUrl;

    /**
     * @var string
     */
    public $borderColor;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        $settings['solutionType'] = array('Sole', 'Mark');
        $settings['landingPage'] = array('Billing', 'Login');
        $settings['brandName'] = '';
        $settings['headerImageUrl'] = '';
        $settings['logoImageUrl'] = '';
        $settings['borderColor'] = '';

        return Craft::t('commerce', 'PayPal Express');
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('commerce-paypal/express/gatewaySettings', ['gateway' => $this]);
    }


    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createGateway(): AbstractGateway
    {
        /** @var Gateway $gateway */
        $gateway = Omnipay::create($this->getGatewayClassName());

        $gateway->setUsername($this->user);
        $gateway->setPassword($this->password);
        $gateway->setSignature($this->signature);
        $gateway->setTestMode($this->testMode);
        $gateway->setSolutionType($this->solutionType);
        $gateway->setLandingPage($this->landingPage);
        $gateway->setBrandName($this->brandName);
        $gateway->setHeaderImageUrl($this->headerImageUrl);
        $gateway->setLogoImageUrl($this->logoImageUrl);
        $gateway->setBorderColor($this->borderColor);

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

    /**
     * @inheritdoc
     */
    protected function getRequest(Transaction $transaction, BasePaymentForm $form = null)
    {
        // For authorize and capture we're referring to a transaction that already took place so no card or item shenanigans.
        if (in_array($transaction->type, [TransactionRecord::TYPE_REFUND, TransactionRecord::TYPE_CAPTURE], false)) {
            $request = $this->createPaymentRequest($transaction);
        } else {
            $order = $transaction->getOrder();
            $itemBag = $this->getItemBagForOrder($order);
            $request = $this->createPaymentRequest($transaction, null, $itemBag);
            $this->populateRequest($request, $form);
        }

        return $request;
    }
}
