<?php
/**
 * Copyright © Magento2Express. All rights reserved.
 * @author: <mailto:contact@magento2express.com>.
 */

namespace M2express\ProductFilter\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $productAttributeRepository;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
    ) {
        parent::__construct($context);
        $this->productAttributeRepository = $attributeRepository;
    }

    /**
     * Attribute return options value
     * @param $code
     * @return \Magento\Eav\Api\Data\AttributeOptionInterface[]|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductAttributes($code)
    {
        $attributeRepository = $this->productAttributeRepository->get($code);
        $customAttributes = $attributeRepository->getOptions();
        $nameOfAttribute = $attributeRepository->getDefaultFrontendLabel();

        $values = [];
        foreach ($customAttributes as $attribute) {
            //$manufacturerOption->getValue();  // Value
            if ($attribute->getValue() != "") {
                $values[] = ['label' => $attribute->getLabel(), 'value' => $attribute->getValue()];
            }
        }
        $returnValue = ['code'=>$code, 'name'=>$nameOfAttribute, 'data'=>$values];

        return $returnValue;
    }
}