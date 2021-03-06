<?php

namespace Ls\Omni\Controller\Stock;

/**
 * Class Store
 * @package Ls\Omni\Controller\Stock
 */

class Store extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Request\Http\Proxy
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session\Proxy
     */
    protected $_session;

    /**
     * @var \Ls\Omni\Helper\StockHelper
     */
    protected $_stockHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Store constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Request\Http\Proxy $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session\Proxy $session
     * @param \Ls\Omni\Helper\StockHelper $stockHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Request\Http\Proxy $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session\Proxy $session,
        \Ls\Omni\Helper\StockHelper $stockHelper
    ) {
        $this->_request = $request;
        $this->_scopeConfig = $scopeConfig;
        $this->_session = $session;
        $this->_stockHelper = $stockHelper;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * execute
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->_resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $selectedStore = $this->_request->getParam('storeid');
            $items = $this->_session->getQuote()->getAllVisibleItems();
            $stockCollection = [];
            $notAvailableNotice = __(
                "Please check other stores or remove
                 the not available item(s) from your "
            );
            foreach ($items as $item) {
                $sku = $item->getSku();
                $parentProductSku = $childProductSku = "";
                if (strpos($sku, '-') !== false) {
                    $parentProductSku = explode('-', $sku)[0];
                    $childProductSku = explode('-', $sku)[1];
                } else {
                    $parentProductSku = $sku;
                }
                $response = $this->_stockHelper->getItemStockInStore(
                    $selectedStore,
                    $parentProductSku,
                    $childProductSku
                );
                if ($response->getInventoryResponse()
                    ->getQtyActualInventory()) {
                    $stockCollection[] = [
                        "name" => $item->getName(),
                        "status" => "1",
                        "display" => __("This item is available")
                    ];
                } else {
                    $stockCollection[] = [
                        "name" => $item->getName(),
                        "status" => "0",
                        "display" => __("This item is not available")
                    ];
                }
            }
            $result = $result->setData(
                ["remarks" => $notAvailableNotice, "stocks" => $stockCollection]
            );
        }
        return $result;
    }
}
