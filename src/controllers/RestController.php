<?php

namespace craft\commerce\paypal\controllers;

use Craft;
use craft\commerce\Plugin as Commerce;
use craft\web\Controller as BaseController;
use yii\web\Response;

/**
 * This controller provides functionality for PayPal REST gateway
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  1.0
 */

class RestController extends BaseController
{
    protected $allowAnonymous = true;

    /**
     * Load bucket data for specified credentials.
     *
     * @return Response
     */
    public function actionPreparePayment()
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $order = Commerce::getInstance()->getCart()->getCart();

        $request = Craft::$app->getRequest();
        $keyId = $request->getRequiredBodyParam('keyId');
        $secret = $request->getRequiredBodyParam('secret');

        try {
            return $this->asJson(Volume::loadBucketList($keyId, $secret));
        } catch (\Throwable $e) {
            return $this->asErrorJson($e->getMessage());
        }
    }
}
