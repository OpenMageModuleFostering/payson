<?php

class Payson_Payson_Model_Method_Invoice extends Payson_Payson_Model_Method_Abstract {
    /*
     * Protected properties
     */

    /**
     * @inheritDoc
     */
    protected $_code = 'payson_invoice';

    /**
     * @inheritDoc
     */
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canVoid = true;
    protected $_canUseCheckout = false;

    /*
     * Public methods
     */

    /**
     * @inheritDoc
     */
    
    public function capture(Varien_Object $payment, $amount) {

        $order = $payment->getOrder();

        $order_id = $order->getData('increment_id');

        $api = Mage::helper('payson/api');
        $helper = Mage::helper('payson');
        $api->PaymentDetails($order_id);
        $details = $api->GetResponse();

        if (($details->type ===
                Payson_Payson_Helper_Api::PAYMENT_METHOD_INVOICE) ||
                ($details->invoiceStatus ===
                Payson_Payson_Helper_Api::INVOICE_STATUS_ORDERCREATED)) {
            $api->PaymentUpdate($order_id, Payson_Payson_Helper_Api::UPDATE_ACTION_SHIPORDER);

            $order->addStatusHistoryComment($helper->__(
                            'Order was activated at Payson'));
        } else {
            Mage::throwException($helper->__('Payson is not ready to create an invoice. Please try again later.'));
        }

        return $this;
    }   
}
