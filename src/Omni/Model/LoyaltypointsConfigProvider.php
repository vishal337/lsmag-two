<?php

namespace Ls\Omni\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class LoyaltypointsConfigProvider implements ConfigProviderInterface
{

    /** @var string  */
    protected $methodCode = Loyaltypoints::PAYMENT_METHOD_LOYALTYPOINTS_CODE;

    /** @var \Magento\Payment\Model\MethodInterface  */
    protected $method;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Checkout\Model\Session\Proxy
     */
    protected $_checkoutSession;

    /**
     * LoyaltypointsConfigProvider constructor.
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession
    ) {
        $this->escaper = $escaper;
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return array
     */
    public function getConfig()
    {

        return $this->method->isAvailable($this->_checkoutSession->getQuote()) ? [
            'payment' => [
                'loyaltypoints' => [
                    'mailingAddress' => $this->getMailingAddress(),
                    'payableTo' => $this->getPayableTo(),
                ],
            ],
        ] : [];
    }
}
