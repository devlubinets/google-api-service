<?php

namespace GoogleAdsApiService\Resources;

use Google\Ads\GoogleAds\Lib\V12\GoogleAdsClient;
use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Common\CustomParameter;
use Google\Ads\GoogleAds\V12\Common\ManualCpc;
use Google\Ads\GoogleAds\V12\Enums\AdvertisingChannelTypeEnum\AdvertisingChannelType;
use Google\Ads\GoogleAds\V12\Enums\AppCampaignBiddingStrategyGoalTypeEnum\AppCampaignBiddingStrategyGoalType;
use Google\Ads\GoogleAds\V12\Enums\BiddingStrategyTypeEnum\BiddingStrategyType;
use Google\Ads\GoogleAds\V12\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use Google\Ads\GoogleAds\V12\Enums\CampaignExperimentTypeEnum\CampaignExperimentType;
use Google\Ads\GoogleAds\V12\Enums\CampaignPrimaryStatusEnum\CampaignPrimaryStatus;
use Google\Ads\GoogleAds\V12\Enums\CampaignServingStatusEnum\CampaignServingStatus;
use Google\Ads\GoogleAds\V12\Enums\CampaignStatusEnum\CampaignStatus;
use Google\Ads\GoogleAds\V12\Resources\Campaign\NetworkSettings;
use Google\Ads\GoogleAds\V12\Resources\CampaignBudget;
use Google\Ads\GoogleAds\V12\Services\CampaignBudgetOperation;
use Google\Ads\GoogleAds\V12\Services\CampaignOperation;
use Google\Ads\GoogleAds\V12\Resources\Campaign as GoogleCampaign;


class Campaign extends Blueprint\Resource
{
    private array $params = [];

    /**
     * @param $googleAdsClient
     * @param int $customerId
     */
    public function __construct($googleAdsClient, int $customerId)
    {
        parent::__construct($googleAdsClient, $customerId);
    }


    private function createNetwork(array $params): NetworkSettings
    {
        return new NetworkSettings([
            'target_google_search' => $params['target_google_search'],
            'target_search_network' => $params['target_search_network'],
            'target_content_network' => $params['target_content_network'],
            'target_partner_search_network' => $params['target_partner_search_network']
        ]);
    }

    /**
     * @return array
     */
    public function getUrlCustomParameters(): array
    {
        return $this->urlCustomParameters;
    }

    /**
     * @param $urlCustomParameter
     */
    private function addUrlCustomParameters($urlCustomParameter): void
    {
        $this->urlCustomParameters[] = $urlCustomParameter;
    }


    /**
     * Create custom parameter for URL
     *
     *  The list of mappings used to substitute custom parameter tags in a
     * `tracking_url_template`, `final_urls`, or `mobile_final_urls`.
     *
     */
    private function createCustomParameters()
    {
        $customParams = $this->getParams()['params'];

        if (empty($customParams)) {
            return [];
        }

        foreach ($customParams as $item) {
            $this->urlCustomParameters[] = new CustomParameter([
                'key' => $item['key'],
                'value' => $item['value']
            ]);
        }

        return $this->urlCustomParameters;
    }

    /**
     * Create new campaign
     *
     * @param string $name
     * @param array $networks
     * @return mixed
     */
    public function create(array $params, array $budgetParams): string
    {
        $urlCustomParameters = $this->createCustomParameters();
        $budgetResourceName = $this->addCampaignBudget($budgetParams);

        $networkSettings = $this->createNetwork($params);
        $campaignOperations = [];

        // Creates a campaign.
        $campaign = new \Google\Ads\GoogleAds\V12\Resources\Campaign([
            'name' => $params['name'],
            'primary_status' => CampaignPrimaryStatus::LEARNING,
            'status' => CampaignStatus::PAUSED,
            'serving_status' => CampaignServingStatus::NONE,
            'bidding_strategy_system_status' => AppCampaignBiddingStrategyGoalType::UNKNOWN,
            'advertising_channel_type' => AdvertisingChannelType::SEARCH,
//            'tracking_url_template' => 'http://www.example.com/tracking?u={lpurl}',
            'url_custom_parameters' => $urlCustomParameters, // [] is empty
            'experiment_type' => CampaignExperimentType::EXPERIMENT,
            'campaign_budget' => $budgetResourceName,
            'bidding_strategy_type' => BiddingStrategyType::UNSPECIFIED,
            'network_settings' => $networkSettings,
            'manual_cpc' => new ManualCpc(),
            'start_date' => date('Ymd', strtotime('+1 day')),
            'end_date' => date('Ymd', strtotime('+1 month')),
        ]);

        // Creates a campaign operation.
        $campaignOperation = new CampaignOperation();
        $campaignOperation->setCreate($campaign);
        $campaignOperations[] = $campaignOperation;

        // Issues a mutate request to add campaigns.
        $campaignServiceClient = $this->getClient()->getCampaignServiceClient();
        $response = $campaignServiceClient->mutateCampaigns($this->getCustomerId(), $campaignOperations);

        printf("Added %d campaigns:%s", $response->getResults()->count(), PHP_EOL);

        return ($response->getResults()[0])->getResourceName();
    }

    /**
     * Update campaign
     *
     * @param string $campaignResourceName
     * @return mixed
     * @throws \Google\ApiCore\ApiException
     */
    public function update(string $campaignResourceName)
    {
        $campaign = new GoogleCampaign([
            'resource_name' =>  $campaignResourceName,
            'status' => CampaignStatus::ENABLED,
        ]);

        $campaignOperation = new CampaignOperation();
        $campaignOperation->setUpdate($campaign);
        $campaignOperation->setUpdateMask(FieldMasks::allSetFieldsOf($campaign));

        $campaignServiceClient = $this->getClient()->getCampaignServiceClient();
        $response = $campaignServiceClient->mutateCampaigns(
            $this->getCustomerId(),
            [$campaignOperation]
        );

        $updatedCampaign = $response->getResults()[0];

        return $updatedCampaign;
    }

    /**
     * Creates a new campaign budget in the specified client account.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the customer ID
     * @return string the resource name of the newly created budget
     */
    public function addCampaignBudget(array $params): string
    {
        $budget = new CampaignBudget([
            'name' => $params['name'],
            'delivery_method' => BudgetDeliveryMethod::STANDARD,
            'amount_micros' => $params['amount_micros']
        ]);

        // Creates a campaign budget operation.
        $campaignBudgetOperation = new CampaignBudgetOperation();
        $campaignBudgetOperation->setCreate($budget);

        // Issues a mutate request.
        $campaignBudgetServiceClient = $this->getClient()->getCampaignBudgetServiceClient();
        $response = $campaignBudgetServiceClient->mutateCampaignBudgets(
            $this->getCustomerId(),
            [$campaignBudgetOperation]
        );

        $addedBudget = $response->getResults()[0];

        return $addedBudget->getResourceName();
    }

    /**
     * Get params for create campaign
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set params for create campaign
     *
     * @param array $paramsCampaign
     * @param array $paramsBudget
     */
    private function setParams(array $paramsCampaign,
                               array $paramsBudget,
                               array $paramsNetwork,
                               array $paramsCustomParams): void
    {
        $this->params['network'] = $paramsNetwork;
        $this->params['campaign'] = $paramsCampaign;
        $this->params['budget'] = $paramsBudget;
        $this->params['params'] = $paramsCustomParams;
    }

    /**
     * Check and set default values for
     * params needed for create entities
     *
     * @return Campaign
     */
    public function withParams(array $paramsCampaign,
                               array $paramsBudget,
                               array $paramsNetwork,
                               array $paramsCustomParams
    ): Campaign
    {
        //todo add check for params
        $this->setParams($paramsCampaign, $paramsBudget, $paramsNetwork, $paramsCustomParams);

        return $this;
    }
}