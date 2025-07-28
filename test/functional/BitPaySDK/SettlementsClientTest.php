<?php

/**
 * Copyright (c) 2019 BitPay
 **/

declare(strict_types=1);

namespace BitPaySDK\Functional;

use BitPaySDK\Model\Settlement\Settlement;

class SettlementsClientTest extends AbstractClientTestCase
{
    public function testGetSettlements(): void
    {
        $status = 'processing';
        $dateStart = date('Y-m-d', strtotime("-50 day"));
        $dateEnd = date("Y-m-d", strtotime("+1 day"));
        $currency = 'USD';

        $settlements = $this->client->getSettlements($currency, $dateStart, $dateEnd, $status);

        self::assertNotNull($settlements);
        self::assertIsArray($settlements);
    }

    public function testGetSettlement(): void
    {
        $dateStart = date('Y-m-d', strtotime("-365 day"));
        $status = 'processing';
        $dateEnd = date("Y-m-d", strtotime("+1 day"));
        $currency = 'USD';

        $settlements = $this->client->getSettlements($currency, $dateStart, $dateEnd, $status);

        // Skip test if no settlements exist
        if (empty($settlements)) {
            error_log(PHP_EOL . 'No settlements found in test account. ' .
                'Skipping test. To test this functionality, ensure your test account has processed transactions.');
            $this->markTestSkipped(
                'No settlements found in test account. ' .
                    'To test this functionality, ensure your test account has processed transactions.'
            );
            return;
        }

        $settlement = $this->client->getSettlement($settlements[0]->getId());

        self::assertNotNull($settlement);
        self::assertInstanceOf(Settlement::class, $settlement);
        self::assertEquals($currency, $settlement->getCurrency());
        self::assertEquals($status, $settlement->getStatus());
    }

    public function testGetReconciliationReport(): void
    {
        $status = 'processing';
        $dateStart = date('Y-m-d', strtotime("-365 day"));
        $dateEnd = date("Y-m-d", strtotime("+1 day"));
        $currency = 'USD';

        $settlements = $this->client->getSettlements($currency, $dateStart, $dateEnd, $status);

        if (empty($settlements)) {
            error_log( PHP_EOL . 'No settlements found in test account. ' .
                'Skipping test. To test this functionality, ensure your test account has processed transactions.
            ');
            $this->markTestSkipped(
                'No settlements found in test account. ' .
                    'To test this functionality, ensure your test account has processed transactions.'
            );
            return;
        }

        $settlement = $this->client->getSettlement($settlements[0]->getId());
        $settlementId = $settlement->getId();
        $token = $settlement->getToken();
        $reconciliationReport = $this->client->getSettlementReconciliationReport($settlementId, $token);

        self::assertNotNull($reconciliationReport);
        self::assertEquals($currency, $reconciliationReport->getCurrency());
        self::assertEquals($status, $reconciliationReport->getStatus());
    }
}
