<?php

declare(strict_types=1);

namespace Square\Models;

use stdClass;

class CatalogCustomAttributeDefinitionNumberConfig implements \JsonSerializable
{
    /**
     * @var int|null
     */
    private $precision;

    /**
     * Returns Precision.
     *
     * An integer between 0 and 5 that represents the maximum number of
     * positions allowed after the decimal in number custom attribute values
     * For example:
     *
     * - if the precision is 0, the quantity can be 1, 2, 3, etc.
     * - if the precision is 1, the quantity can be 0.1, 0.2, etc.
     * - if the precision is 2, the quantity can be 0.01, 0.12, etc.
     *
     * Default: 5
     */
    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    /**
     * Sets Precision.
     *
     * An integer between 0 and 5 that represents the maximum number of
     * positions allowed after the decimal in number custom attribute values
     * For example:
     *
     * - if the precision is 0, the quantity can be 1, 2, 3, etc.
     * - if the precision is 1, the quantity can be 0.1, 0.2, etc.
     * - if the precision is 2, the quantity can be 0.01, 0.12, etc.
     *
     * Default: 5
     *
     * @maps precision
     */
    public function setPrecision(?int $precision): void
    {
        $this->precision = $precision;
    }

    /**
     * Encode this object to JSON
     *
     * @param bool $asArrayWhenEmpty Whether to serialize this model as an array whenever no fields
     *        are set. (default: false)
     *
     * @return array|stdClass
     */
    #[\ReturnTypeWillChange] // @phan-suppress-current-line PhanUndeclaredClassAttribute for (php < 8.1)
    public function jsonSerialize(bool $asArrayWhenEmpty = false)
    {
        $json = [];
        if (isset($this->precision)) {
            $json['precision'] = $this->precision;
        }
        $json = array_filter($json, function ($val) {
            return $val !== null;
        });

        return (!$asArrayWhenEmpty && empty($json)) ? new stdClass() : $json;
    }
}
