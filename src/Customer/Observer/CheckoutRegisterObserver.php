<?php

namespace Ls\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ls\Omni\Helper\ContactHelper;

/**
 * Class CheckoutRegisterObserver
 * @package Ls\Customer\Observer
 */
class CheckoutRegisterObserver implements ObserverInterface
{
    /** @var ContactHelper */
    private $contactHelper;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var \Magento\Customer\Model\Session $customerSession */
    protected $customerSession;
    /** @var \Magento\Checkout\Model\Session */
    protected $checkoutSession;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $orderRepository;
    /** @var \Magento\Customer\Model\CustomerFactory */
    protected $customerFactory;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;
    /** @var \Magento\Customer\Model\ResourceModel\Customer */
    protected $customerResourceModel;

    /**
     * CheckoutRegisterObserver constructor.
     * @param ContactHelper $contactHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
     */

    public function __construct(
        ContactHelper $contactHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
    ) {
        $this->contactHelper = $contactHelper;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->customerResourceModel = $customerResourceModel;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $orderId = $this->checkoutSession->getLastOrderId();
        $order = $this->orderRepository->get($orderId);
        if ($order->getCustomerId()) {
            // only performed when a customer id is created
            $customer = $this->customerFactory->create()
                ->setWebsiteId($this->storeManager->getWebsite()->getWebsiteId())
                ->loadByEmail($order->getCustomerEmail());
            // setting the lsr_username field
            $customer->setData('lsr_username', $customer->getEmail());
            // manually set the password for now and generate the reset password link
            $customer->setData('password', 'admin@123');
            /* var $contact = Ls/Omni/Client/Ecommerce/Entity/MemberContact */
            $contact = $this->contactHelper->contact($customer);
            if (is_object($contact) && $contact->getId()) {
                $token = $contact->getLoggedOnToDevice()->getSecurityToken();
                $card = $contact->getCard();
                $customer->setData('lsr_id', $contact->getId());
                $customer->setData('lsr_token', $token);
                $customer->setData('lsr_cardid', $card->getId());

                if ($contact->getAccount()->getScheme()->getId()) {
                    $customerGroupId      =   $this->contactHelper->getCustomerGroupIdByName(
                        $contact->getAccount()->getScheme()->getId()
                    );
                    $customer->setGroupId($customerGroupId);
                }
                $result = $this->contactHelper->forgotPassword($customer->getEmail());
                if ($result) {
                    $customer->setData('lsr_resetcode', $result);
                }
                $this->customerResourceModel->save($customer);
            }
        }
    }
}
