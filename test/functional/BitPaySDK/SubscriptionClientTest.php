<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace BitPaySDK\Functional;

use BitPaySDK\Model\Subscription\Item;
use BitPaySDK\Model\Currency;
use BitPaySDK\Model\Subscription\BillData;
use BitPaySDK\Model\Subscription\Subscription;
use BitPaySDK\Model\Subscription\SubscriptionSchedule;
use BitPaySDK\Model\Subscription\SubscriptionStatus;

class SubscriptionClientTest extends AbstractClientTestCase
{
    public function testCreateSubscription(): void
    {
        $subscription = $this->getSubscriptionExample();
        $subscription = $this->client->createSubscription($subscription);

        // Subscription tests
        self::assertEquals(SubscriptionStatus::DRAFT, $subscription->getStatus());
        self::assertEquals(SubscriptionSchedule::MONTHLY, $subscription->getSchedule());

        // BillData tests
        self::assertEquals(3.0, $subscription->getBillData()->getItems()[0]->getPrice());
        self::assertEquals(2, $subscription->getBillData()->getItems()[0]->getQuantity());
        self::assertEquals(1, $subscription->getBillData()->getItems()[1]->getQuantity());
        self::assertEquals(7.0, $subscription->getBillData()->getItems()[1]->getPrice());
        self::assertEquals("Test Item 1", $subscription->getBillData()->getItems()[0]->getDescription());
        self::assertEquals("Test Item 2", $subscription->getBillData()->getItems()[1]->getDescription());
        self::assertEquals(Currency::USD, $subscription->getBillData()->getCurrency());
    }

    public function testGetSubscription(): void
    {
        $subscription = $this->getSubscriptionExample();
        $subscription = $this->client->createSubscription($subscription);
        $subscription = $this->client->getSubscription($subscription->getId());

        self::assertEquals(SubscriptionStatus::DRAFT, $subscription->getStatus());
        self::assertCount(2, $subscription->getBillData()->getItems());
        self::assertEquals(Currency::USD, $subscription->getBillData()->getCurrency());
        self::assertEquals('billData1234-ABCD', $subscription->getBillData()->getNumber());
        self::assertEquals('john.doe@example.com', $subscription->getBillData()->getEmail());
    }

    public function testGetSubscriptions(): void
    {
        $subscriptions = $this->client->getSubscriptions();

        self::assertNotNull($subscriptions);
        self::assertIsArray($subscriptions);
        $isCount = count($subscriptions) > 0;
        self::assertTrue($isCount);
    }

    public function testUpdateSubscription(): void
    {
        $subscription = $this->getSubscriptionExample();
        $subscription = $this->client->createSubscription($subscription);
        $subscription = $this->client->getSubscription($subscription->getId());

        $bill = $subscription->getBillData();
        $bill->setEmail("jane.doe@example.com");
        $subscription->setBillData($bill);

        $subscription = $this->client->updateSubscription($subscription, $subscription->getId());

        self::assertEquals("jane.doe@example.com", $subscription->getBillData()->getEmail());
    }

    private function getSubscriptionExample(): Subscription
    {
        return new Subscription($this->getBillDataExample());
    }

    private function getBillDataExample(): BillData
    {
        $items = [];
        $item = new Item();
        $item->setPrice(3.0);
        $item->setQuantity(2);
        $item->setDescription("Test Item 1");
        $items[] = $item;

        $item = new Item();
        $item->setPrice(7.0);
        $item->setQuantity(1);
        $item->setDescription("Test Item 2");
        $items[] = $item;

        return new BillData("billData1234-ABCD", Currency::USD, "john.doe@example.com", null, $items);
    }
}
