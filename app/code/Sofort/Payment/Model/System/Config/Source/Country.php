<?php

/**
 * Copyright (c) 2008-2017 dotSource GmbH.
 * All rights reserved.
 * http://www.dotsource.de
 *
 * Contributors:
 * Stefan Jauck - initial contents
 */

namespace Sofort\Payment\Model\System\Config\Source;

/**
 * Class Country
 * @package Sofort\Payment\Model\System\Config\Source
 */
class Country implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Countries
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $countryCollection;

    /**
     * @var array
     */
    protected $excludedCountries = [
        'MM', 'IR', 'SD', 'BY', 'CI', 'CD', 'CG', 'IQ', 'LR', 'LB', 'KP', 'SL', 'SY', 'ZW', 'AL', 'BA', 'MK', 'ME', 'RS'
    ];

    /**
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
     */
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
    ) {
        $this->countryCollection = $countryCollection;
    }

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->options) {
            $this->options = $this->countryCollection
                ->addFieldToFilter('country_id', ['nin' => $this->excludedCountries])
                ->loadData()
                ->toOptionArray(false);
        }

        $options = $this->options;
        if (!$isMultiselect) {
            array_unshift($options, ['value'=>'', 'label'=> __('--Please Select--')]);
        }

        return $options;
    }

    /**
     * If country is in list of restricted (not supported by Braintree)
     *
     * @param string $countryId
     * @return boolean
     */
    public function isCountryRestricted($countryId)
    {
        if (in_array($countryId, $this->excludedCountries)) {
            return true;
        }
        return false;
    }

    /**
     * Returns list of restricted countries
     *
     * @return array
     */
    public function getRestrictedCountries()
    {
        return $this->excludedCountries;
    }

}
