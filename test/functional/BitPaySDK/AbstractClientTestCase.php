<?php

declare(strict_types=1);

namespace BitPaySDK\Functional;

use BitPaySDK\Client;
use BitPaySDK\Exceptions\BitPayGenericException;
use PHPUnit\Framework\TestCase;

abstract class AbstractClientTestCase extends TestCase
{
    protected Client $client;

    // Default delay in seconds between API calls
    private const API_CALL_DELAY = 0.5;
    private static $lastApiCallTime = 0;

    /**
     * @throws BitPayGenericException
     */
    public function setUp(): void
    {
        // Add delay to respect rate limits
        $this->respectRateLimit();

        $this->client = Client::createWithFile(
            Config::FUNCTIONAL_TEST_PATH . DIRECTORY_SEPARATOR . Config::BITPAY_CONFIG_FILE
        );
    }

    /**
     * Delays execution if needed to respect rate limits
     */
    protected function respectRateLimit(): void
    {
        $currentTime = microtime(true);
        $timeSinceLastCall = $currentTime - self::$lastApiCallTime;

        if (self::$lastApiCallTime > 0 && $timeSinceLastCall < self::API_CALL_DELAY) {
            $sleepTime = (self::API_CALL_DELAY - $timeSinceLastCall);
            usleep((int)($sleepTime * 1000000)); // Convert to microseconds
        }

        self::$lastApiCallTime = microtime(true);
    }
}
