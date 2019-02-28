<?php

namespace craft\commerce\paypal\gateways;

use Craft;
use craft\commerce\base\RequestResponseInterface;
use craft\commerce\errors\PaymentException;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\Transaction;
use craft\commerce\paypal\PayPalExpressBundle;
use craft\commerce\omnipay\base\OffsiteGateway;
use craft\web\View;
use Omnipay\Common\AbstractGateway;
use Omnipay\Omnipay;
use Omnipay\PayPal\PayPalItemBag;
use Omnipay\PayPal\ExpressInContextGateway as Gateway;

/**
 * PayPalRest represents the PayPal Rest gateway
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since     1.0
 */
class PayPalExpress extends OffsiteGateway
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $username;

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

    /**
     * @var bool Whether cart information should be sent to the payment gateway
     */
    public $sendCartInfo = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function completeAuthorize(Transaction $transaction): RequestResponseInterface
    {
        $request = $this->_prepareOffsiteTransactionConfirmationRequest($transaction);
        $completeRequest = $this->prepareCompleteAuthorizeRequest($request);

        return $this->performRequest($completeRequest, $transaction);
    }

    /**
     * @inheritdoc
     */
    public function completePurchase(Transaction $transaction): RequestResponseInterface
    {
        $request = $this->_prepareOffsiteTransactionConfirmationRequest($transaction);
        $completeRequest = $this->prepareCompletePurchaseRequest($request);

        return $this->performRequest($completeRequest, $transaction);
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('commerce', 'PayPal Express');
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('commerce-paypal/express/gatewaySettings', ['gateway' => $this]);
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
        $view->registerAssetBundle(PayPalExpressBundle::class);

        $html = Craft::$app->getView()->renderTemplate('commerce-paypal/express/paymentForm', $params);
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
        $gateway = static::createOmnipayGateway($this->getGatewayClassName());

        $gateway->setUsername($this->username);
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

    /**
     * @inheritdoc
     */
    protected function getItemBagClassName(): string
    {
        return PayPalItemBag::class;
    }

    // Private Methods
    // =========================================================================

    /**
     * Prepare the confirmation request for completeAuthorize and completePurchase requests.
     *
     * @param Transaction $transaction
     * @return array
     * @throws PaymentException if missing parameters
     */
    private function _prepareOffsiteTransactionConfirmationRequest(Transaction $transaction): array
    {
        $request = $this->createRequest($transaction);

        $token = Craft::$app->getRequest()->getParam('token');
        $payerId = Craft::$app->getRequest()->getParam('PayerID');

        if (!$token) {
            throw new PaymentException('Missing token');
        }

        $request['token'] = $token;

        if (!$payerId) {
            throw new PaymentException('Missing payer ID');
        }
        $request['PayerID'] = $payerId;

        return $request;
    }
}
