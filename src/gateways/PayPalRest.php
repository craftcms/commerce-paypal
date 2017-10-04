<?php

namespace craft\commerce\paypal\gateways;

use Craft;
use craft\commerce\base\RequestResponseInterface;
use craft\commerce\errors\PaymentException;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\Transaction;
use craft\commerce\paypal\PayPalRestBundle;
use craft\commerce\omnipay\base\OffsiteGateway;
use craft\web\View;
use Omnipay\Common\AbstractGateway;
use Omnipay\Omnipay;
use Omnipay\PayPal\PayPalItemBag;
use Omnipay\PayPal\RestGateway as Gateway;

/**
 * Stripe represents the Stripe gateway
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since     1.0
 */
class PayPalRest extends OffsiteGateway
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
    public function completeAuthorize(Transaction $transaction): RequestResponseInterface
    {
        return $this->completePurchase($transaction);
    }

    /**
     * @inheritdoc
     */
    public function completePurchase(Transaction $transaction): RequestResponseInterface
    {
        $request = $this->createRequest($transaction);

        $paymentId = Craft::$app->getRequest()->getParam('paymentId');

        if (!$paymentId) {
            throw new PaymentException('Missing payment ID');
        }

        $request['transactionReference'] = $paymentId;

        if (empty($request['PayerID'])) {
            $payerId = Craft::$app->getRequest()->getParam('PayerID');
            if (!$payerId) {
                throw new PaymentException('Missing payer ID');
            }
            $request['PayerID'] = $payerId;
        }

        $completeRequest = $this->prepareCompletePurchaseRequest($request);

        return $this->performRequest($completeRequest, $transaction);
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'PayPal REST');
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
    public function getPaymentFormHtml(array $params)
    {
        $defaults = [
            'gateway' => $this
        ];

        $params = array_merge($defaults, $params);

        $view = Craft::$app->getView();

        $previousMode = $view->getTemplateMode();
        $view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $view->registerJsFile('https://www.paypalobjects.com/api/checkout.js');
        $view->registerAssetBundle(PayPalRestBundle::class);

        $html = Craft::$app->getView()->renderTemplate('commerce-paypal/rest/paymentForm', $params);
        $view->setTemplateMode($previousMode);

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function populateRequest(array &$request, BasePaymentForm $paymentForm = null)
    {
        unset($request['card']);
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

        $gateway->setClientId($this->clientId);
        $gateway->setSecret($this->secret);
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

    /**
     * @inheritdoc
     */
    protected function getItemBagClassName(): string
    {
        return PayPalItemBag::class;
    }
}
