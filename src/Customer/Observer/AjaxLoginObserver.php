<?php

namespace Ls\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zend_Validate;
use Zend_Validate_EmailAddress;
use Ls\Omni\Helper\ContactHelper;
use Ls\Omni\Client\Ecommerce\Entity;
use Ls\Core\Model\LSR;
use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * Class AjaxLoginObserver
 * @package Ls\Customer\Observer
 */
class AjaxLoginObserver implements ObserverInterface
{

    /** @var ContactHelper  */
    private $contactHelper;

    /** @var \Magento\Framework\Api\FilterBuilder  */
    protected $filterBuilder;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder  */
    protected $searchCriteriaBuilder;

    /** @var \Magento\Customer\Api\CustomerRepositoryInterface   */
    protected $customerRepository;

    /** @var \Magento\Framework\Message\ManagerInterface  */
    protected $messageManager;

    /** @var \Magento\Framework\Registry  */
    protected $registry;

    /** @var \Psr\Log\LoggerInterface  */
    protected $logger;

    /** @var \Magento\Customer\Model\Session  */
    protected $customerSession;

    /** @var \Magento\Framework\App\ActionFlag */
    protected $actionFlag;

    /** @var \Magento\Framework\Json\Helper\Data $jsonhelper */
    protected $jsonhelper;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManage;

    /** @var \Magento\Customer\Model\CustomerFactory */
    protected $customerFactory;

    /** @var \Magento\Checkout\Model\Session  */
    protected $checkoutSession;

    /** @var  \Ls\Omni\Helper\BasketHelper  */
    protected $basketHelper;

    /** @var \Magento\Customer\Model\ResourceModel\Customer */
    protected $customerResourceModel;

    protected $resultJsonFactory;

    /**
     * AjaxLoginObserver constructor.
     * @param ContactHelper $contactHelper
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Registry $registry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Json\Helper\Data $jsonhelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ls\Omni\Helper\BasketHelper $basketHelper
     * @param \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
     */
    public function __construct(
        ContactHelper $contactHelper,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Json\Helper\Data $jsonhelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ls\Omni\Helper\BasketHelper $basketHelper,
        \Magento\Customer\Model\ResourceModel\Customer $customerResourceModel
    ) {
        $this->contactHelper = $contactHelper;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->jsonhelper = $jsonhelper;
        $this->actionFlag = $actionFlag;
        $this->storeManage = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->checkoutSession  =   $checkoutSession;
        $this->basketHelper     =   $basketHelper;
        $this->customerResourceModel = $customerResourceModel;
    }

    /**
     * @param Observer $observer
     * @return $this|\Magento\Framework\Controller\Result\Json
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Validate_Exception
     */

    public function execute(Observer $observer)
    {
        try {
            /** @var $request \Magento\Framework\App\RequestInterface */
            $request = $observer->getEvent()->getRequest();

            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();

            // check if we have a data in request and request is Ajax.
            if ($request && $request->isXmlHttpRequest()) {
                $credentials = $this->jsonhelper->jsonDecode($request->getContent());
                $email = $username = $credentials['username'];
                $websiteId = $this->storeManage->getWebsite()->getWebsiteId();
                $is_email = Zend_Validate::is($username, Zend_Validate_EmailAddress::class);
                // CASE FOR EMAIL LOGIN := TRANSLATION TO USERNAME
                if ($is_email) {
                    $search = $this->contactHelper->search($username);
                    $found = !is_null($search)
                        && ($search instanceof Entity\MemberContact)
                        && !empty($search->getEmail());
                    if (!$found) {
                        $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                        $response = [
                            'errors' => true,
                            'message' => __('Sorry. No account found with the provided email address')
                        ];
                        $observer->getControllerAction()->getResponse()->representJson(
                            $this->jsonhelper->jsonEncode($response)
                        );
                        return $this;
                    }
                    $email = $search->getEmail();
                }

                if ($is_email) {
                    $filters = [$this->filterBuilder
                        ->setField('email')
                        ->setConditionType('eq')
                        ->setValue($email)
                        ->create()];
                    $this->searchCriteriaBuilder->addFilters($filters);
                    $searchCriteria = $this->searchCriteriaBuilder->create();
                    $searchResults = $this->customerRepository->getList($searchCriteria);

                    if ($searchResults->getTotalCount() == 0) {
                        $response = [
                            'errors' => true,
                            'message' => __(
                                'Unfortunately email login is only available for members registered in Magento'
                            )
                        ];
                        $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                        $observer->getControllerAction()->getResponse()->representJson(
                            $this->jsonhelper->jsonEncode($response)
                        );
                        return $this;
                    } else {
                        $customerObj = null;
                        foreach ($searchResults->getItems() as $match) {
                            $customerObj = $this->customerFactory->create()->setWebsiteId($websiteId)
                                ->loadByEmail($email);
                            break;
                        }
                        $username = $customerObj->getData('lsr_username');
                    }
                }

                $result = $this->contactHelper->login($username, $credentials['password']);

                if ($result == false) {
                    $response = [
                        'errors' => true,
                        'message' => __('Invalid Omni login or Omni password')
                    ];
                    $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    $observer->getControllerAction()
                        ->getResponse()
                        ->representJson($this->jsonhelper->jsonEncode($response));
                    return $this;
                }
                $response = [
                    'errors' => false,
                    'message' => __('Omni login successful.')
                ];
                if ($result instanceof Entity\MemberContact) {
                    $filters = [$this->filterBuilder
                        ->setField('email')
                        ->setConditionType('eq')
                        ->setValue($result->getEmail())
                        ->create()];
                    $this->searchCriteriaBuilder->addFilters($filters);
                    $searchCriteria = $this->searchCriteriaBuilder->create();
                    $searchResults = $this->customerRepository->getList($searchCriteria);
                    $customer = null;
                    if ($searchResults->getTotalCount() == 0) {
                        $customer = $this->contactHelper->customer($result, $credentials['password']);
                    } else {
                        foreach ($searchResults->getItems() as $match) {
                            $customer = $this->customerRepository->getById($match->getId());
                            break;
                        }
                    }
                    $customer_email = $customer->getEmail();
                    $websiteId = $this->storeManage->getWebsite()->getWebsiteId();
                    /** @var \Magento\Customer\Model\Customer $customer */
                    $customer = $this->customerFactory->create()
                        ->setWebsiteId($websiteId)
                        ->loadByEmail($customer_email);
                    $card = $result->getCard();
                    if (is_null($customer->getData('lsr_id'))) {
                        $customer->setData('lsr_id', $result->getId());
                    }
                    if (!$is_email && empty($customer->getData('lsr_username'))) {
                        $customer->setData('lsr_username', $username);
                    }
                    if (is_null($customer->getData('lsr_cardid'))) {
                        $customer->setData('lsr_cardid', $card->getId());
                    }
                    $token = $result->getLoggedOnToDevice()->getSecurityToken();

                    $customer->setData('lsr_token', $token);
                    $customer->setData('attribute_set_id', CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER);

                    if ($result->getAccount()->getScheme()->getId()) {
                        $customerGroupId = $this->contactHelper->getCustomerGroupIdByName(
                            $result->getAccount()->getScheme()->getId()
                        );
                        $customer->setGroupId($customerGroupId);
                    }

                    $this->customerResourceModel->save($customer);
                    $this->registry->register(LSR::REGISTRY_LOYALTY_LOGINRESULT, $result);
                    $this->customerSession->setData(LSR::SESSION_CUSTOMER_SECURITYTOKEN, $token);
                    $this->customerSession->setData(LSR::SESSION_CUSTOMER_LSRID, $result->getId());

                    $card = $result->getCard();
                    if ($card instanceof Entity\Card && !is_null($card->getId())) {
                        $this->customerSession->setData(LSR::SESSION_CUSTOMER_CARDID, $card->getId());
                    }

                    $this->customerSession->setCustomerAsLoggedIn($customer);

                    /** @var Entity\OneList $oneListBasket */
                    $oneListBasket = $result->getBasket();

                    $quote = $this->checkoutSession->getQuote();


                    if (!is_array($oneListBasket) and $oneListBasket instanceof Entity\OneList and $oneListBasket->getId() != '') {
                        // If customer has previously one list created then get that and sync the current information with that.

                        // store the onelist returned from Omni into Magento session.
                        $this->customerSession->setData(LSR::SESSION_CART_ONELIST, $oneListBasket);

                        // update items from quote to basket.
                        $oneList = $this->basketHelper->setOneListQuote($quote, $oneListBasket);

                        // update the onelist to Omni.
                        $this->basketHelper->update($oneList);
                    } elseif ($this->customerSession->getData(LSR::SESSION_CART_ONELIST)) {
                        // if customer already has onelist created then update the list to get the information with user.


                        $oneListBasket = $this->customerSession->getData(LSR::SESSION_CART_ONELIST);

                        //Update onelist in Omni with user data.

                        $oneListBasket->setCardId($card->getId())
                            ->setContactId($result->getId())
                            ->setDescription('OneList Magento')
                            ->setIsDefaultList(true)
                            ->setListType(Entity\Enum\ListType::BASKET);

                        // update items from quote to basket.
                        $oneList = $this->basketHelper->setOneListQuote($quote, $oneListBasket);
                        // update the onelist to Omni.

                        $this->basketHelper->update($oneList);
                    } elseif (count($quote->getAllItems()) > 0) {
                        // get the onelist or if not exist then create new one with empty data of customer.

                        $oneList = $this->basketHelper->get();

                        $oneList = $this->basketHelper->setOneListQuote($quote, $oneList);

                        $this->basketHelper->update($oneList);
                    }


                    $this->customerSession->regenerateId();
                    $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    return $resultJson->setData($response);
                } else {
                    $response = [
                        'errors' => true,
                        'message' => __('The service is currently unavailable. Please try again later.')
                    ];
                    $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    $observer->getControllerAction()
                        ->getResponse()
                        ->representJson($this->jsonhelper->jsonEncode($response));
                    return $this;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $this;
    }
}
