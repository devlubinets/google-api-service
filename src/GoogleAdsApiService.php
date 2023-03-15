<?php

namespace GoogleAdsApiService;

use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Common\ManualCpc;
use Google\Ads\GoogleAds\V12\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V12\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V12\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V12\Resources\Campaign;
use Google\Ads\GoogleAds\V12\Resources\Campaign\NetworkSettings;
use Google\Ads\GoogleAds\V12\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V12\Resources\CampaignDraft;
use Google\Ads\GoogleAds\V12\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V12\Services\CampaignDraftOperation;
use Google\Ads\GoogleAds\V12\Services\CampaignOperation;

class GoogleAdsApiService
{
    /**
     * Google Ads Service Client
     * @var GoogleAdsClient
     */
    private GoogleAdsClient $client;

    private OAuth2TokenBuilder $oAuth2Credential;

    private GoogleAdsClient $googleAdsClient;

    private GoogleAdsClient $c;

    private string $clientRefreshToken;

    private string $clientSecret;

    private string $clientId;

    private string $customerId;

    private string $loginCustomerId;

    private string $developerToken;

    public function __construct($config)
    {
        $this->developerToken = $config['developerToken'];
        $this->customerId = $config['customerId'];
        $this->clientRefreshToken = $config['clientRefreshToken'];
        $this->clientId = $config['clientId'];
        $this->clientSecret = $config['clientSecret'];
        $this->loginCustomerId = $config['loginCustomerId'];
        $this->configuration();
    }

    /**
     * Dynamic configuration
     *
     * @return void
     */
    private function configuration()
    {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId($this->clientId)
            ->withClientSecret($this->clientSecret)
            ->withRefreshToken($this->clientRefreshToken)
            ->build();

        $this->googleAdsClient = (new GoogleAdsClientBuilder())
            ->withDeveloperToken($this->developerToken)
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId($this->loginCustomerId)
            ->build();
    }

    public function getGoogleClient()
    {
        return $this->googleAdsClient;
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