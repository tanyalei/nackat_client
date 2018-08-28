<?php

namespace NationalCatalogApi;

final class Entry
{
    private $entry = [];

    public function setInternalId($id)
    {
        $this->entry['@id'] = $id;
    }

    public function addCategory($catId)
    {
        if (!isset($this->entry['categories'])) {
            $this->entry['categories'] = [];
        } 
        $this->entry['categories'][] = ['cat_id' => $catId];
    }

    public function deleteCategory($catId)
    {
        if (!isset($this->entry['categories'])) {
            $this->entry['categories'] = [];
        } 
        $this->entry['categories'][] = ['cat_id' => $catId, 'delete' => 1];
    }

    public function addIdentifedBy($type, $value, $partyId = null, $level = "trade-unit", $multiplier =1)
    {
        $identifiedBy = [
            'type' => $type, 
            'value' => $value, 
            'multiplier' => $multiplier,
            'level' => $level
        ];
        if ($partyId) {
            $identifiedBy['partyId'] = $partyId;
        }
        if (!isset($this->entry['identified_by'])) {
            $this->entry['identified_by'] = [];
        } 
        $this->entry['identified_by'][] = $identifiedBy;
    }

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

    public function setGoodId($goodId)
    {
        $this->entry['good_id'] = $goodId;
    }

    public function setGoodName($goodName)
    {
        $this->entry['good_name'] = $goodName;
    }

    public function setBrandId($brandId)
    {
        $this->entry['brand_id'] = $brandId;
    }


    public function toArray()
    {
        return $this->entry;
    }
}