<?php

namespace Sofort\Payment\Block\Info;

use Magento\Framework\View\Element\Template;

/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */
class Sofort extends \Magento\Payment\Block\Info
{
    public function __construct(Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->setTemplate('sofort/payment/info/sofort.phtml');
    }

}