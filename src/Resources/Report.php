<?php

namespace GoogleAdsApiService\Resources;

use Google\Ads\GoogleAds\V12\Resources\Customer;
use Google\ApiCore\ApiException;

class Report extends Blueprint\Resource
{

    private array $result = [];

    /**
     * Get report of campaign on each params:
     *
     * Return next properties:
     * - Campaign ID
     * - Campaign name
     * - Impressions
     * - Clicks
     * - Conversation
     * - Cost
     *
     * @return array
     * @throws ApiException
     */
    public function getCampaignReport(): array
    {
        $this->setResult();
        $googleAdsServiceClient = $this->getClient()->getGoogleAdsServiceClient();

        $query = 'SELECT campaign.id, campaign.name, 
                         metrics.impressions, metrics.clicks, metrics.conversions, metrics.cost_micros
                  FROM campaign';

        $stream = $googleAdsServiceClient->searchStream($this->getCustomerId(), $query);

        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $campaign = $googleAdsRow->getCampaign();
            $metrics = $googleAdsRow->getMetrics();

            $this->result['id'] = $campaign->getId();
            $this->result['name'] = $campaign->getName();
            $this->result['impressions'] = $metrics->getImpressions();
            $this->result['clicks'] = $metrics->getClicks();
            $this->result['conversions'] = $metrics->getConversions();
            $this->result['cost_micros'] = $metrics->getCostMicros();
        }

        return $this->getResult();
    }

    /**
     * Get report of customer on each params:
     *
     * Return next properties:
     * - Customer ID
     * - Customer name
     * - Currency Code
     *
     * @return array
     */
    public function getCustomerReport(): array
    {
        $this->setResult();
        $googleAdsServiceClient = $this->getClient()->getGoogleAdsServiceClient();
        $customerId = $this->getCustomerId();

        $query = "SELECT customer.currency_code, customer.id, customer.descriptive_name 
                  FROM customer 
                  WHERE customer.id = $customerId LIMIT 1";

        $response = $googleAdsServiceClient->search($customerId, $query);

        $customer = new Customer();

        foreach ($response->iterateAllElements() as $googleAdsRow) {
            $customer = $googleAdsRow->getCustomer();
        }

        $currencyCode = $customer->getCurrencyCode();
        $accountId = $customer->getId();
        $accountName = $customer->getDescriptiveName();

        $this->result['id'] = $accountId;
        $this->result['name'] = $accountName;
        $this->result['currency_code'] = $currencyCode;

        return $this->getResult();
    }

    /**
     * Get report of group on each params:
     *
     * Return next properties:
     * - group ID
     * - group name
     * - Impressions
     * - Clicks
     * - Conversation
     * - Cost
     *
     * @return array
     * @throws ApiException
     */
    public function getGroupReport(int $campaignId): array
    {
        $this->setResult();
        $googleAdsServiceClient = $this->getClient()->getGoogleAdsServiceClient();

        $query = "SELECT ad_group.id, ad_group.name, metrics.impressions, metrics.clicks, metrics.conversions, metrics.cost_micros
                  FROM ad_group
                  WHERE campaign.id = $campaignId";

        $stream = $googleAdsServiceClient->searchStream($this->getCustomerId(), $query);

        foreach ($stream->iterateAllElements() as $googleAdsRow) {
            $group = $googleAdsRow->getAdGroup();
            $metrics = $googleAdsRow->getMetrics();

            $this->result['id'] = $group->getId();
            $this->result['name'] = $group->getName();
            $this->result['impressions'] = $metrics->getImpressions();
            $this->result['clicks'] = $metrics->getClicks();
            $this->result['conversions'] = $metrics->getConversions();
            $this->result['cost_micros'] = $metrics->getCostMicros();
        }

        return $this->getResult();
    }

    /**
     * Get report of ad on each params:
     *
     * Return next properties:
     * - Ad ID
     * - Ad name
     * - Impressions
     * - Clicks
     * - Conversation
     * - Cost
     *
     * @return array
     * @throws ApiException
     */
    public function getAdReport(int $id): array
    {
        $this->setResult();
        $googleAdsServiceClient = $this->getClient()->getGoogleAdsServiceClient();

        $query = "SELECT ad_group_ad.ad.id, ad_group_ad.ad.name, 
                         metrics.impressions, metrics.clicks, metrics.conversions
                  FROM ad_group_ad
                  WHERE ad_group_ad.ad.id = $id";

        $stream = $googleAdsServiceClient->search($this->getCustomerId(), $query);
        $response = $stream->getIterator()->current();

        $ad = $response->getAdGroupAd()->getAd();
        $metrics = $response->getMetrics();

        $this->result['id'] = $ad->getId();
        $this->result['name'] = $ad->getName();
        $this->result['impressions'] = $metrics->getImpressions();
        $this->result['clicks'] = $metrics->getClicks();
        $this->result['conversions'] = $metrics->getConversions();

        return $this->getResult();
    }

    /*
     * Init result
     *
     */
    private function setResult(): void
    {
        unset($this->result);
        $this->result = [];
    }

    /**
     * Get result
     *
     * @return array
     */
    private function getResult(): array
    {
        return $this->result;
    }


}