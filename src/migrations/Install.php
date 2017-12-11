<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\commerce\paypal\migrations;

use Craft;
use craft\commerce\paypal\gateways\PayPalExpress;
use craft\commerce\paypal\gateways\PayPalPro;
use craft\commerce\paypal\gateways\PayPalRest;
use craft\db\Migration;
use craft\db\Query;
use craft\helpers\Json;

/**
 * Installation Migration
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  1.0
 */
class Install extends Migration
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Convert any built-in Paypal gateways to ours
        $this->_convertGateways();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return true;
    }

    // Private Methods
    // =========================================================================

    /**
     * Converts any old school PayPal gateways to this one
     *
     * @return void
     * @throws \yii\db\Exception
     */
    private function _convertGateways()
    {
        $gateways = (new Query())
            ->select(['id', 'settings'])
            ->where(['type' => 'craft\\commerce\\gateways\\PayPal_Pro'])
            ->from(['{{%commerce_gateways}}'])
            ->all();

        $dbConnection = Craft::$app->getDb();

        foreach ($gateways as $gateway) {

            $settings = Json::decode($gateway['settings']);

            if (!empty($settings['username'])) {
                $settings['user'] = $settings['username'];
                unset($settings['username']);
            }

            $values = [
                'type' => PayPalPro::class,
                'settings' => Json::encode($settings)
            ];

            $dbConnection->createCommand()
                ->update('{{%commerce_gateways}}', $values, ['id' => $gateway['id']])
                ->execute();
        }

        $gateways = (new Query())
            ->select(['id'])
            ->where(['type' => 'craft\\commerce\\gateways\\PayPal_Express'])
            ->from(['{{%commerce_gateways}}'])
            ->all();
 
        $dbConnection = Craft::$app->getDb();
 
        foreach ($gateways as $gateway) {
            $values = [
                'type' => PayPalExpress::class,
            ];

            $dbConnection->createCommand()
                ->update('{{%commerce_gateways}}', $values, ['id' => $gateway['id']])
                ->execute();
        }
    }
}
