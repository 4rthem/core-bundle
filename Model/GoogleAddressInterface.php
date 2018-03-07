<?php

namespace Arthem\Bundle\CoreBundle\Model;

interface GoogleAddressInterface
{
    /**
     * Get the entered address by the user.
     *
     * @return string|null
     */
    public function getInputAddress();

    /**
     * Set the entered address by the user.
     *
     * @param string|null $address
     */
    public function setInputAddress(string $address = null);

    /**
     * @return string|null
     */
    public function getAddressLine1();

    /**
     * @param string|null $addressLine1
     */
    public function setAddressLine1(string $addressLine1 = null);

    /**
     * @return string|null
     */
    public function getAddressLine2();

    /**
     * @param string|null $addressLine2
     */
    public function setAddressLine2(string $addressLine2 = null);

    /**
     * @return string|null
     */
    public function getCity();

    /**
     * @param string|null $city
     */
    public function setCity(string $city = null);

    /**
     * @return string|null
     */
    public function getRegion();

    /**
     * @param string|null $region
     */
    public function setRegion(string $region = null);

    /**
     * @return string|null
     */
    public function getPostalCode();

    /**
     * @param string|null $postalCode
     */
    public function setPostalCode(string $postalCode = null);

    /**
     * @return string|null
     */
    public function getCountry();

    /**
     * @param string|null $country
     */
    public function setCountry(string $country = null);

    /**
     * @return float|null
     */
    public function getLatitude();

    /**
     * @param string|float|null $latitude
     */
    public function setLatitude($latitude = null);

    /**
     * @return float|null
     */
    public function getLongitude();

    /**
     * @param string|float|null $longitude
     */
    public function setLongitude($longitude = null);
}
