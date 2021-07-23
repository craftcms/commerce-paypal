<?php

namespace craft\commerce\paypal;

use craft\commerce\paypal\gateways\PayPalExpress;
use craft\commerce\paypal\gateways\PayPalRest;
use craft\commerce\paypal\gateways\PayPalPro;
use craft\commerce\services\Gateways;
use craft\events\RegisterComponentTypesEvent;
use yii\base\Event;


/**
 * Plugin represents the Stripe integration plugin.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  1.0
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Event::on(
            Gateways::class,
            Gateways::EVENT_REGISTER_GATEWAY_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = PayPalPro::class;
                $event->types[] = PayPalRest::class;
                $event->types[] = PayPalExpress::class;
            }
        );
    }
}
