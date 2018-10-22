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
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\ObjectManagerInterface;
use M2express\ProductFilter\Helper\Data;

class Filter extends \Magento\Framework\View\Element\Template
{
    protected $_scopeConfig;
    protected $attributesFilter;
    protected $filterHelper;
    protected $filterableAttributeList;
    protected $filterList;
    protected $_layerResolver;
    protected $layerState;
    protected $_objectManager;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        FilterableAttributeList $filterableAttributeList,
        FilterList $filterList,
        Resolver $resolver,
        ObjectManagerInterface $objectManager,
        Data $filterHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->filterHelper = $filterHelper;
        $this->filterableAttributeList = $filterableAttributeList;
        $this->filterList = $filterList;
        $this->_layerResolver = $resolver->get();
        $this->_objectManager = $objectManager;
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
     * Get all filterable attributes
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

    /**
     * Get filter from layer resolver by category id
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLayerFilterable()
    {
        $homeCategoryId = $this->_scopeConfig->getValue(
            'm2express_home2steps/general/home_category',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
        $this->_layerResolver->setCurrentCategory($homeCategoryId);
        $fill = $this->_objectManager->create(FilterableAttributeList::class);
        $filterList = new FilterList($this->_objectManager, $fill);
        $filterAttributes = $filterList->getFilters($this->_layerResolver);
        $filterArray = [];
        $i = 0;

        foreach ($filterAttributes as $filter) {
            $items = $filter->getItems(); //Gives all available filter options in that particular filter
            $filterValues = [];
            $j = 0;
            foreach ($items as $item) {
                $filterValues[$j]['display'] = strip_tags($item->getLabel());
                $filterValues[$j]['label'] = $item->getValue();
                $filterValues[$j]['count'] = $item->getCount(); //Gives no. of products in each filter options
                $j++;
            }
            if (!empty($filterValues)) {
                $availableFilter = ['code' => $filter->getRequestVar(),
                    'name' => $filter->getName(), 'data' => $filterValues];
                $filterArray[] =  $availableFilter;
            }
            $i++;
        }
        return $filterArray;
    }
}
