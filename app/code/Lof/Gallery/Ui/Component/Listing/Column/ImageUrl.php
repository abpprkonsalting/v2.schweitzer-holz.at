<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Gallery
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Gallery\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Lof\Gallery\Model\Banner as BannerModel;

class ImageUrl extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'image';

    const ALT_FIELD = 'name';

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /** @var bannerModel */
    protected $bannerModel;

    /**
     * @param ContextInterface                           $context            
     * @param UiComponentFactory                         $uiComponentFactory 
     * @param \Magento\Catalog\Helper\Image              $imageHelper        
     * @param \Magento\Framework\UrlInterface            $urlBuilder         
     * @param \Magento\Framework\Filesystem              $filesystem         
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager       
     * @param BannerModel                                 $bannerModel         
     * @param array                                      $components         
     * @param array                                      $data               
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        BannerModel $bannerModel,
        array $components = [],
        array $data = []
        ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->bannerModel = $bannerModel;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {

        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        //$mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        //$mediaFolder = 'lof/gallery/';

        //$path = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        
        if (isset($dataSource['data']['items']))
        {            

            $fieldName = $this->getData('name');
            
            foreach ($dataSource['data']['items'] as &$item) {
                if(!isset($item['file'])) continue;
                if($item['link']){
                    $imageUrl = $item['link'];

                    $item[$fieldName . '_src'] = $imageUrl;
                    $item[$fieldName . '_alt'] = $item['label'];
                    $item[$fieldName . '_link'] = $imageUrl;
                    $item[$fieldName . '_orig_src'] = $imageUrl;
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
    }
}
