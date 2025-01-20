<?php

namespace Zeleri\WooCommerce\Zeleripay\Blocks\ZeleriPaymentMethodTB;
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WCGatewayZeleriTBWebpayBlocks extends AbstractPaymentMethodType {

    use WCGatewayZeleriTBBlocks;

    protected $name = 'zeleri_pay_payment_gateways_tb';

    public function __construct() {
        $this->scriptInfo = array('dependencies' => array('wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities'), 'version' => '04778d2c588251b3173574f6332df2000');
        $this->paymentId = $this->name;
        $this->productName = 'zeleri_tb';
    }
}
