<?php

namespace NationalCatalogApi;

final class Entry
{
    /**
     * @var array
     */
    private $entry = [];

    /**
     * @param mixed $id
     */
    public function setInternalId($id)
    {
        $this->entry['@id'] = $id;
    }

    /**
     * @param int $catId
     */
    public function addCategory($catId)
    {
        if (!isset($this->entry['categories'])) {
            $this->entry['categories'] = [];
        }
        $this->entry['categories'][] = ['cat_id' => $catId];
    }

    /**
     * @param int $catId
     */
    public function deleteCategory($catId)
    {
        if (!isset($this->entry['categories'])) {
            $this->entry['categories'] = [];
        }
        $this->entry['categories'][] = ['cat_id' => $catId, 'delete' => 1];
    }

    /**
     * @param string $type
     * @param string $value
     * @param int $partyId
     * @param string $level
     * @param int $multiplier
     */
    public function addIdentifiedBy($type, $value, $partyId = null, $level = "trade-unit", $multiplier = 1)
    {
        $identifiedBy = [
            'type' => $type,
            'value' => $value,
            'multiplier' => $multiplier,
            'level' => $level
        ];
        if ($partyId) {
            $identifiedBy['party_id'] = $partyId;
        }
        if (!isset($this->entry['identified_by'])) {
            $this->entry['identified_by'] = [];
        }
        $this->entry['identified_by'][] = $identifiedBy;
    }

    /**
     * @param int $attrId
     * @param mixed $attrValue
     * @param string $attrValueType
     * @param int $attrValueId
     */
    public function addAttr($attrId, $attrValue, $attrValueType = null, $attrValueId = null)
    {
        $attr = [
            'attr_id' => $attrId,
            'attr_value' => $attrValue
        ];
        if ($attrValueType) {
            $attr['attr_value_type'] = $attrValueType;
        }
        if ($attrValueId) {
            $attr['attr_value_id'] = $attrValueId;
        }
        if (!isset($this->entry['good_attrs'])) {
            $this->entry['good_attrs'] = [];
        }
        $this->entry['good_attrs'][] = $attr;
    }

    /**
     * @param string $type
     * @param string|array $url
     * @param int $locationId
     */
    public function addImage($type, $url, $locationId = null)
    {
        $image = [
            'photo_type' => $type,
            'photo_url' => $url
        ];

        if ($locationId) {
            $image['location_id'] = $locationId;
        }

        if (!isset($this->entry['good_images'])) {
            $this->entry['good_images'] = [];
        }
        $this->entry['good_images'][] = $image;
    }

    /**
     * @param int $goodId
     */
    public function setGoodId($goodId)
    {
        $this->entry['good_id'] = $goodId;
    }

    /**
     * @param string $goodName
     */
    public function setGoodName($goodName)
    {
        $this->entry['good_name'] = $goodName;
    }

    /**
     * @param int $brandId
     */
    public function setBrandId($brandId)
    {
        $this->entry['brand_id'] = $brandId;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->entry;
    }
}