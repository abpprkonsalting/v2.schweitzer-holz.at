<?php
/**
 * @author Cynoinfotech Team
 * @package Cynoinfotech_ShipPerProduct
 */
namespace Cynoinfotech\ShipPerProduct\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Catalog\Model\ProductFactory;

class Cishipperproduct extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'cishipperproduct';
    
    protected $_logger;
    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_logger = $logger;
        $this->productFactory = $productFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $rateForPerOrder=[];
        $shippingPrice=0;
        $enabledDefaultRate=$this->getConfigData('enable_default_rate');
        
        if ($enabledDefaultRate) {
            $defaultRate=$this->getConfigData('default_rate');
        } else {
            $defaultRate=0;
        }
        $handlingFee=$this->getConfigData('handling_fee');
        $methodType=$this->getConfigData('method_type');
        $orderMaxMin=$this->getConfigData('order_max_min');
        
        $cartItems = $request->getAllItems();
        if ($cartItems) {
            $result = $this->_rateResultFactory->create();
            
            foreach ($cartItems as $cartItem) {
                $_product = $this->productFactory->create()->load($cartItem->getProductId());
                $_productQty   =  $cartItem->getQty();
                $type_id = $_product->getTypeId();
                $this->_logger->addDebug("item Product Type ".$cartItem->getProductType());
                $this->_logger->addDebug("Product Type ".$type_id);
                 
                if ($cartItem->getIsVirtual() ||
                    $type_id == 'virtual' ||
                    $type_id == 'downloadable' ||
                    $type_id == 'configurable' ||
                    $type_id == 'bundle') {
                    continue;
                }
                
                $perproduct_rate = $_product->getShippingRatePerproduct();
                
                if ($perproduct_rate=="") {
                    $perproduct_rate=$defaultRate;
                    
                    if ($cartItem->getParentItem()) {
                        $parentProductId=$cartItem->getParentItem()->getProductId();
                    }
                    
                    if ($cartItem->getProductType()=='grouped') {
                        $values = $cartItem->getOptionByCode('info_buyRequest')->getValue();
                        $values = json_decode($values);
                        $parentProductId = $values->super_product_config->product_id;
                    }
                    
                    if (isset($parentProductId) && !empty($parentProductId)) {
                        $this->_logger->addDebug("Parent product ID ".$parentProductId);
                        $_parentProduct = $this->productFactory->create()->load($parentProductId);
                        $parent_perproduct_rate = $_parentProduct->getShippingRatePerproduct();
                        $this->_logger->addDebug("Parent Per Product Rate ".$parent_perproduct_rate);
                        if ($parent_perproduct_rate!="") {
                            $perproduct_rate=$parent_perproduct_rate;
                        }
                    }
                }
                
                $this->_logger->addDebug("Per Product Rate ".$perproduct_rate);
                
                $rateForPerOrder[] = $perproduct_rate;
                
                if ($this->getConfigData('qty_multiply')) {
                    $shippingPrice += ($_productQty * $perproduct_rate);
                } else {
                    $shippingPrice += $perproduct_rate;
                }
            }
        }
        
        if ($methodType==0) {
            // Method Type Per Order
            if ($orderMaxMin== "min") {
                $shippingPrice = min($rateForPerOrder);
            } else {
                $shippingPrice = max($rateForPerOrder);
            }
        }
        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);
        
        $enableFreeShipping=$this->getConfigData('enable_free_shipping');
        if ($enableFreeShipping) {
            $freeShippingOverTotal=$this->getConfigData('free_shipping_over_total');
            $subtotalInclTax = $request->getBaseSubtotalInclTax();
            if ($freeShippingOverTotal < $subtotalInclTax) {
                $shippingPrice = '0.00';
            }
        }
        
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);
        $result->append($method);
        
        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        
        return [$this->_code=> $this->getConfigData('name')];
    }
}
