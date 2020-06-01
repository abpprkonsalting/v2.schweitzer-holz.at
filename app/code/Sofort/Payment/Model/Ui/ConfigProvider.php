<?php
/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = "sofortueberweisung";

    public function getConfig()
    {

    }

}
