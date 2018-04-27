<?php

namespace Pim\Component\Catalog\Normalizer\Standard\Product;

use Pim\Component\Catalog\Model\AssociationAwareInterface;
use Pim\Component\Catalog\Model\AssociationInterface;
use Pim\Component\Catalog\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalize associations into an array
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ParentsAssociationsNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($associationAwareEntity, $format = null, array $context = [])
    {
        $parentAssociations = $this->getParentAssociations($associationAwareEntity);
        $data = [];

        foreach ($parentAssociations as $association) {
            $code = $association->getAssociationType()->getCode();
            $data[$code]['groups'] = [];
            foreach ($association->getGroups() as $group) {
                $data[$code]['groups'][] = $group->getCode();
            }

            $data[$code]['products'] = [];
            foreach ($association->getProducts() as $product) {
                $data[$code]['products'][] = $product->getReference();
            }

            $data[$code]['product_models'] = [];
            foreach ($association->getProductModels() as $productModel) {
                $data[$code]['product_models'][] = $productModel->getCode();
            }
        }

        ksort($data);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AssociationAwareInterface && 'standard' === $format;
    }

    /**
     * @param ProductInterface $product
     *
     * @return AssociationInterface[]
     */
    public function getParentAssociations(ProductInterface $product): array
    {
        $parent = $product->getParent();
        $parentAssociations = [];

        if (null === $parent) {
            return $parentAssociations;
        }

        foreach ($parent->getAssociations() as $association) {
            $parentAssociations[] = $association;
        }

        return $parentAssociations;
    }
}
