<?php

/**
 * Copyright (c) 2025 BitPay
 **/

declare(strict_types=1);

namespace BitPaySDK\Model\Subscription;

use BitPaySDK\Env;
use BitPaySDK\Exceptions\BitPayExceptionProvider;
use BitPaySDK\Exceptions\BitPayValidationException;
use BitPaySDK\Model\Currency;

/**
 * @package BitPaySDK\Model\Subscription
 * @author BitPay Integrations <integrations@bitpay.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class BillData
{
    protected ?bool $emailBill = null;
    protected ?array $cc = null;
    protected ?string $number = null;
    protected ?string $currency = null;
    protected ?string $name = null;
    protected ?string $address1 = null;
    protected ?string $address2 = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $zip = null;
    protected ?string $country = null;
    protected ?string $email = null;
    protected ?string $phone = null;
    protected ?string $dueDate = null;
    protected ?bool $passProcessingFee = null;
    protected array $items = [];

    /**
     * Constructor, create a minimal request BillData object.
     *
     * @param string|null $number Recurring bill identifier, specified by merchant
     * @param string|null $currency The three digit currency code used to compute the billData's amount.
     * @param string|null $email The email address of the receiver for this billData.
     * @param string|\DateTime|null $dueDate Date and time at which a bill is due, ISO-8601 format yyyy-mm-ddThh:mm:ssZ
     *                                       (UTC).
     * @param array|null $items The list of line items to add to this billData.
     */
    public function __construct(
        ?string $number = null,
        ?string $currency = null,
        ?string $email = null,
        string|\DateTime|null $dueDate = null,
        ?array $items = null
    ) {
        $this->number = $number;
        $this->currency = $currency ?: Currency::USD;
        $this->email = $email;
        $this->setEmailBill();

        if (!$dueDate || is_a($dueDate, \DateTime::class)) {
            $dueDate = ($dueDate ? new \DateTime('now') : $dueDate)->format(Env::BITPAY_DATETIME_FORMAT);
        }
        $this->dueDate = $dueDate;

        if (!$items) {
            $items = [];
        }
        $this->setItems($items);
    }

    /**
     * Gets billData emailBill
     *
     * If set to `true`, BitPay will automatically issue recurring bills to the `email` address provided once the
     * status of the subscription is set to `active`.
     *
     * @return bool|null
     */
    public function getEmailBill(): ?bool
    {
        return $this->emailBill;
    }

    /**
     * Sets billData's emailBill
     *
     * If set to `true`, BitPay will automatically issue recurring bills to the `email` address provided once the
     * status of the subscription is set to `active`.
     *
     * @param bool|null $emailBill
     * @return void
     */
    public function setEmailBill(?bool $emailBill = true): void
    {
        $this->emailBill = $emailBill;
    }

    /**
     * Gets items from billData
     *
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Sets BillData's items
     *
     * @param Item[] $items List of line items
     */
    public function setItems(array $items): void
    {
        $itemsArray = [];

        foreach ($items as $item) {
            if ($item instanceof Item) {
                $itemsArray[] = $item;
            } else {
                $itemsArray[] = Item::createFromArray((array)$item);
            }
        }

        $this->items = $itemsArray;
    }

    /**
     * Gets items as array from billData
     *
     * @return array|null items as array from billData
     */
    public function getItemsAsArray(): ?array
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = $item->toArray();
        }

        return $items;
    }

    /**
     * Get billData data as array
     *
     * @return array billData data as array
     */
    public function toArray(): array
    {
        $elements = [
            'emailBill' => $this->emailBill,
            'cc' => $this->getCc(),
            'number' => $this->getNumber(),
            'currency' => $this->getCurrency(),
            'name' => $this->getName(),
            'address1' => $this->getAddress1(),
            'address2' => $this->getAddress2(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'zip' => $this->getZip(),
            'country' => $this->getCountry(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'dueDate' => $this->getDueDate(),
            'passProcessingFee' => $this->getPassProcessingFee(),
            'items' => $this->getItemsAsArray(),
        ];

        foreach ($elements as $key => $value) {
            if (empty($value)) {
                unset($elements[$key]);
            }
        }

        return $elements;
    }

    /**
     * Gets BillData cc
     *
     * Email addresses to which a copy of the billData must be sent
     *
     * @return array|null the cc
     */
    public function getCc(): ?array
    {
        return $this->cc;
    }

    /**
     * Sets BillData's cc
     *
     * Email addresses to which a copy of the billData must be sent
     *
     * @param array $cc Email addresses to which a copy of the billData must be sent
     */
    public function setCc(array $cc): void
    {
        $this->cc = $cc;
    }

    /**
     * Gets billData number
     *
     * BillData identifier, specified by merchant
     *
     * @return string|null the number
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * Sets BillData's number
     *
     * BillData identifier, specified by merchant
     *
     * @param string $number BillData identifier, specified by merchant
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /**
     * Gets billData currency
     *
     * ISO 4217 3-character currency code. This is the currency associated with the price field
     *
     * @return string|null the billData currency
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * Sets BillData's currency
     *
     * ISO 4217 3-character currency code. This is the currency associated with the price field
     *
     * @param string $currency 3-character currency code
     * @throws BitPayValidationException
     */
    public function setCurrency(string $currency): void
    {
        if (!Currency::isValid($currency)) {
            BitPayExceptionProvider::throwInvalidCurrencyException($currency);
        }

        $this->currency = $currency;
    }

    /**
     * Gets BillData recipient's name
     *
     * @return string|null the name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Sets BillData recipient's name
     *
     * @param string $name BillData recipient's name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets BillData recipient's address
     *
     * @return string|null the address1
     */
    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * Sets BillData recipient's address
     *
     * @param string $address1 BillData recipient's address
     */
    public function setAddress1(string $address1): void
    {
        $this->address1 = $address1;
    }

    /**
     * Gets BillData recipient's address
     *
     * @return string|null the address2
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * Sets BillData recipient's address
     *
     * @param string $address2 BillData recipient's address
     */
    public function setAddress2(string $address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * Gets BillData recipient's city
     *
     * @return string|null the city
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Sets BillData recipient's city
     *
     * @param string $city BillData recipient's city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * Gets BillData recipient's state or province
     *
     * @return string|null the state
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Sets BillData recipient's state or province
     *
     * @param string $state BillData recipient's state or province
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * Gets BillData recipient's ZIP code
     *
     * @return string|null the zip
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * Sets BillData recipient's ZIP code
     *
     * @param string $zip BillData recipient's ZIP code
     */
    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * Gets BillData recipient's country
     *
     * @return string|null the country
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Sets BillData recipient's country
     *
     * @param string $country BillData recipient's country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * Gets billData email
     *
     * @return string|null the email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets BillData's email
     *
     * @param string $email BillData's email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Gets BillData recipient's phone number
     *
     * @return string|null the phone
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Sets BillData recipient's phone number
     *
     * @param string $phone BillData recipient's phone number
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * Gets BillData due date
     *
     * Date and time at which a billData is due, ISO-8601 format yyyy-mm-ddThh:mm:ssZ. (UTC)
     *
     * @return string|null the number
     * @see Env::BITPAY_DATETIME_FORMAT
     */
    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    /**
     * Sets BillData's due date
     *
     * Date and time at which a billData is due, ISO-8601 format yyyy-mm-ddThh:mm:ssZ. (UTC)
     *
     * @param string $dueDate Date and time at which a billData is due
     * @see Env::BITPAY_DATETIME_FORMAT
     */
    public function setDueDate(string $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    /**
     * Gets billData pass processing fee
     *
     * @return bool|null the pass processing fee
     */
    public function getPassProcessingFee(): ?bool
    {
        return $this->passProcessingFee;
    }

    /**
     * Sets BillData's pass processing fee
     *
     * If set to `true`, BitPay's processing fee will be included in the amount charged on the invoice
     *
     * @param bool $passProcessingFee BillData's pass processing fee
     */
    public function setPassProcessingFee(bool $passProcessingFee): void
    {
        $this->passProcessingFee = $passProcessingFee;
    }
}
