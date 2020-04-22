<?php
namespace ABPPRK\LoadingSpinner\Block\Spinner;

class Spinner extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_helperCustom;
    protected $_filterProvider;

    /**
     * Custom constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,

        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_filterProvider = $filterProvider;

        $this->setTemplate('spinner.phtml');

        parent::__construct($context, $data);
    }

}
