<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Helper;

use Magento\Catalog\Model\Product;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;
use Sofort\Payment\Gateway\Data\Rewrite\Order\OrderAdapter;

/**
 * Class Tests
 * @package Sofort\Payment\Helper
 */
class Tests extends AbstractHelper
{
    /**
     * @param Order $order
     * @return mixed
     */
    public function getOrderAdapter($order = null)
    {
        if (empty($order)) {
            $order = $this->_getOrder();
        }

        return \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(OrderAdapter::class, ['order' => $order]);
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        $products[] = $this->_getProduct();
        /**
         * @var \Magento\Sales\Model\Order $order
         */
        $order = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order::class);

        /**
         * @var \Magento\Quote\Model\Quote\Address $quoteAddress
         */
        $quoteAddress = Bootstrap::getObjectManager()->create('Magento\Quote\Model\Quote\Address');
        $quoteAddress
            ->setPrefix('Herr')
            ->setFirstname('Test')
            ->setLastname('Integration Order Export')
            ->setStreet('Leutragraben 1')
            ->setPostcode('07743')
            ->setCity('Jena')
            ->setCountryId('DE')
            ->setCompany('1')
            ->setRegionCode('Bayern')
            ->setRegionId(1)
            ->setCustomAttribute('street_number', 44)
            ->setIsDefaultBilling(true)
            ->setTelephone('123456');
        $articles = array();
        foreach ($products as $product) {
            /**
             * @var Item $article
             */
            $article = Bootstrap::getObjectManager()->create('Magento\Quote\Model\Quote\Item');
            $article
                ->setName($product->getName())
                ->setProduct($product)
                ->setPrice($product->getPrice());
            $articles[] = $article;
        }
        /**
         * Prepare Quote Currency
         */
        /**
         * @var \Magento\Quote\Model\Cart\Currency $currency
         */
        $currency = Bootstrap::getObjectManager()
            ->get('Magento\Quote\Model\Cart\Currency');
        $currency->setQuoteCurrencyCode('EUR');
        /*
         * prepare Quote
         */
        /**
         * @var \Magento\Quote\Model\Quote $quote
         */
        ShippingMethodConverterTest::class;
        $quote = Bootstrap::getObjectManager()->create('Magento\Quote\Model\Quote');
        $quote
            ->setGrandTotal(205.0000)
            ->setCustomerIsGuest(true)
            ->setBillingAddress($quoteAddress->setStreet('Leutragraben 10'))
            ->setShippingAddress($quoteAddress->setShippingMethod('flatrate_flatrate'))
            ->setCustomerEmail('test@example.com')
            ->setRemoteIp('10.10.0.2')
            ->setStoreId(1)
            ->setIsVirtual(true)
            ->setCurrency($currency);
        $quote->collectTotals();
        $quote->getShippingAddress()->setCollectShippingRates(true)->collectShippingRates();
        $quote->getPayment()->setMethod('banktransfer');
        foreach ($articles as $article) {
            $quote->addItem($article);
        }
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        foreach ($quote->getItemsCollection() as $item) {
            /** @var \Magento\Quote\Model\Quote\ProductOption $productOption */
            $productOption = $objectManager->create('Magento\Catalog\Model\ProductOptionFactory')->create();
            /** @var  \Magento\Quote\Api\Data\ProductOptionExtensionInterface $extensionAttributes */
            $extensionAttributes = $objectManager->create('Magento\Catalog\Api\Data\ProductOptionExtensionFactory')
                ->create();
            $productOption->setExtensionAttributes($extensionAttributes);
        }
        $quote->save();
        sleep(1);
        $orderId = Bootstrap::getObjectManager()->create(\Magento\Quote\Model\QuoteManagement::class)
            ->placeOrder($quote->getId());
        /**
         * Prepare Order
         */
        /**
         * @var Order $order
         */
        $order = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        $order->load($orderId);
        return $order;
    }

    /**
     * @param string $type
     * @param int $attributeSet
     * @param bool $saved
     * @param array $data
     * @param int $status
     * @return Magento\Catalog\Model\Product
     */
    protected function _getProduct(
        $type = 'simple',
        $attributeSet = 1,
        $saved = true,
        $data = array(),
        $status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
    ) {
        /**
         * @var Magento\Catalog\Model\Product $product
         */
        $product = Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
        $product->cleanModelCache()->clearInstance();
        $product->setTypeId(
            $type
        )->setAttributeSetId(
            Bootstrap::getObjectManager()
                ->create(\Magento\Catalog\Model\Config::class)->getAttributeSetId(Product::ENTITY, $attributeSet)
        )->setName(
            'Simple Product ' . $type .' ' . uniqid()
        )->setSku(
            uniqid()
        )->setPrice(
            10
        )->setMetaTitle(
            'meta title'
        )->setMetaKeyword(
            'meta keyword'
        )->setMetaDescription(
            'meta description'
        )->setVisibility(
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
        )->setStatus(
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        );
        if (!empty($data)) {
            $this->_addAttributeData($product, $data);
        }

        if ($saved) {
            $product->save()
                ->setStatus(
                    $status
                )
                ->save();
        }
        return $product;
    }
}
