<?php

namespace GoogleAdsApiService\Resources;

use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Enums\AdGroupStatusEnum\AdGroupStatus;
use Google\Ads\GoogleAds\V12\Enums\AdGroupTypeEnum\AdGroupType;
use Google\Ads\GoogleAds\V12\Resources\AdGroup;
use Google\Ads\GoogleAds\V12\Services\AdGroupOperation;

class Group extends Blueprint\Resource
{
    public function __construct($googleAdsClient, string $customerId)
    {
        parent::__construct($googleAdsClient, $customerId);
    }

    /**
     * Create ad group
     *
     * @param string $name
     * @return string
     * @throws \Google\ApiCore\ApiException
     */
    public function create(string $name, string $campaignResourceName): string
    {
        $operations = [];

        // Constructs an ad group and sets an optional CPC value.
        $adGroup = new AdGroup([
            'name' => $name  . rand(1,20),
            'campaign' => $campaignResourceName,
            'status' => AdGroupStatus::ENABLED,
            'type' => AdGroupType::SEARCH_STANDARD,
            'cpc_bid_micros' => 20000000
        ]);

        $adGroupOperation = new AdGroupOperation();
        $adGroupOperation->setCreate($adGroup);

        $operations[] = $adGroupOperation;

        // Issues a mutate request to add the ad groups.
        $adGroupServiceClient = $this->getClient()->getAdGroupServiceClient();
        $response = $adGroupServiceClient->mutateAdGroups(
            $this->getCustomerId(),
            $operations
        );

        printf("Added %d ad groups:%s", $response->getResults()->count(), PHP_EOL);

        $addedAdGroup = $response->getResults()->getIterator()->current();

        return $addedAdGroup->getResourceName();
    }

    public function update(string $adGroupIdResourceName, $bidMicroAmount)
    {
        // Creates an ad group object with the specified resource name and other changes.
        $adGroup = new AdGroup([
            'resource_name' =>$adGroupIdResourceName,
            'cpc_bid_micros' => $bidMicroAmount,
            'status' => AdGroupStatus::PAUSED
        ]);

        // Constructs an operation that will update the ad group with the specified resource name,
        // using the FieldMasks utility to derive the update mask. This mask tells the Google Ads
        // API which attributes of the ad group you want to change.
        $adGroupOperation = new AdGroupOperation();
        $adGroupOperation->setUpdate($adGroup);
        $adGroupOperation->setUpdateMask(FieldMasks::allSetFieldsOf($adGroup));

        // Issues a mutate request to update the ad group.
        $adGroupServiceClient = $this->getClient()->getAdGroupServiceClient();
        $response = $adGroupServiceClient->mutateAdGroups(
            $this->getCustomerId(),
            [$adGroupOperation]
        );

        // Prints the resource name of the updated ad group.
        /** @var AdGroup $updatedAdGroup */
        $updatedAdGroup = $response->getResults()[0];
        printf(
            "Updated ad group with resource name: '%s'%s",
            $updatedAdGroup->getResourceName(),
            PHP_EOL
        );
    }
}