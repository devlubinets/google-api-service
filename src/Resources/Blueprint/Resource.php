<?php

namespace GoogleAdsApiService\Resources\Blueprint;

use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;

abstract class Resource
{
    private GoogleAdsClient $client;

    private int $customerId;

    /**
     * @param GoogleAdsClient $client
     * @param int $customerId
     */
    public function __construct(GoogleAdsClient $client, int $customerId)
    {
        $this->client = $client;
        $this->customerId = $customerId;
    }

    /**
     * @return GoogleAdsClient
     */
    public function getClient(): GoogleAdsClient
    {
        return $this->client;
    }

    /**
     * @param GoogleAdsClient $client
     */
    public function setClient(GoogleAdsClient $client): void
    {
        $this->client = $client;
    }

    /**
     * @return int
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }
}