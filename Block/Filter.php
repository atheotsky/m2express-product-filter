<?php
/**
 * Copyright © Magento2Express. All rights reserved.
 * @author: <mailto:contact@magento2express.com>.
 */

namespace M2express\ProductFilter\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use M2express\ProductFilter\Helper\Data;

class Filter extends \Magento\Framework\View\Element\Template
{
    protected $_scopeConfig;
    protected $attributesFilter;
    protected $filterHelper;
    protected $filterableAttributeList;
    protected $filterList;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        FilterableAttributeList $filterableAttributeList,
        Data $filterHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->filterHelper = $filterHelper;
        $this->filterableAttributeList = $filterableAttributeList;
        parent::__construct($context);
    }

    /**
     * Get attributes filter from admin config
     * @return mixed
     */
    public function getAttributesFilter()
    {
        $this->attributesFilter = $this->_scopeConfig->getValue(
            'm2express_home2steps/general/filter_attributes',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        $attributes = explode(',', $this->attributesFilter);

        $returnArr = [];

        foreach ($attributes as $attribute) {
            try {
                $attOptions = $this->filterHelper->getProductAttributes($attribute);
                $returnArr[] = $attOptions;
            } catch (NoSuchEntityException $e) {
            }
        }
        return $returnArr;
    }

    /**
     * @return \Magento\Catalog\Model\Layer\Filter\Item[]
     */
    public function getAllFilterableAttributes()
    {
        $filterableList = $this->filterableAttributeList->getList()
            ->addFieldToFilter('is_visible_on_front', ['gt' => 0]);
        $result = [];
        foreach ($filterableList->getData() as $attribute) {
            try {
                $attOptions = $this->filterHelper->getProductAttributes($attribute['attribute_code']);
                $result[] = $attOptions;
            } catch (NoSuchEntityException $e) {
            }
        }

        return $result;
    }
}
