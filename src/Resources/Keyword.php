<?php

namespace GoogleAdsApiService\Resources;

use Google\Ads\GoogleAds\Util\FieldMasks;
use Google\Ads\GoogleAds\Util\V12\ResourceNames;
use Google\Ads\GoogleAds\V12\Common\KeywordInfo;
use Google\Ads\GoogleAds\V12\Enums\AdGroupCriterionStatusEnum\AdGroupCriterionStatus;
use Google\Ads\GoogleAds\V12\Enums\KeywordMatchTypeEnum\KeywordMatchType;
use Google\Ads\GoogleAds\V12\Resources\AdGroupCriterion;
use Google\Ads\GoogleAds\V12\Services\AdGroupCriterionOperation;

class Keyword extends Blueprint\Resource
{
    /**
     * Create keyword
     *
     * @param string $keywordText
     * @param int $adGroupResourceName
     * @throws \Google\ApiCore\ApiException
     */
    public function create(string $keywordText, string $adGroupResourceName)
    {
        // Configures the keyword text and match type settings.
        $keywordInfo = new KeywordInfo([
            'text' => $keywordText,
            'match_type' => KeywordMatchType::EXACT
        ]);

        // Constructs an ad group criterion using the keyword text info above.
        $adGroupCriterion = new AdGroupCriterion([
            'ad_group' => $adGroupResourceName,
            'status' => AdGroupCriterionStatus::ENABLED,
            'keyword' => $keywordInfo
        ]);

        $adGroupCriterionOperation = new AdGroupCriterionOperation();
        $adGroupCriterionOperation->setCreate($adGroupCriterion);

        // Issues a mutate request to add the ad group criterion.
        $adGroupCriterionServiceClient = $this->getClient()->getAdGroupCriterionServiceClient();
        $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria(
            $this->getCustomerId(),
            [$adGroupCriterionOperation]
        );

        printf("Added %d ad group criteria:%s", $response->getResults()->count(), PHP_EOL);

        foreach ($response->getResults() as $addedAdGroupCriterion) {
            /** @var AdGroupCriterion $addedAdGroupCriterion */
            print $addedAdGroupCriterion->getResourceName() . PHP_EOL;
        }

        $addedKeyword = $response->getResults()->getIterator()->current();

        return $addedKeyword->getResourceName();
    }

    /**
     * Update keyword
     *
     * @param string $adGroupCriterionResourceName
     * @return void
     * @throws \Google\ApiCore\ApiException\
     */
    public function update(string $customerId, string $adGroupId, string $criterionId)
    {
        $resourceName = ResourceNames::forAdGroupCriterion(
            $customerId,
            $adGroupId,
            $criterionId
        );

        var_dump($resourceName);

        // Create a new keyword object with the desired parameters
        $adGroupCriterion = new AdGroupCriterion([
            'resource_name' => $resourceName,
            'status' => AdGroupCriterionStatus::ENABLED,
            'keyword' => new KeywordInfo([
                'text' => 'super nuclear motor'
            ])
        ]);

        // Constructs an operation that will update the ad group criterion, using the FieldMasks
        // utility to derive the update mask. This mask tells the Google Ads API which attributes of
        // the ad group criterion you want to change.
        $adGroupCriterionOperation = new AdGroupCriterionOperation();
        $adGroupCriterionOperation->setUpdate($adGroupCriterion);
        $adGroupCriterionOperation->setUpdateMask(FieldMasks::allSetFieldsOf($adGroupCriterion));

        // Issues a mutate request to update the ad group criterion.
        $adGroupCriterionServiceClient = $this->getClient()->getAdGroupCriterionServiceClient();
        $response = $adGroupCriterionServiceClient->mutateAdGroupCriteria(
            $customerId,
            [$adGroupCriterionOperation]
        );

        // Prints the resource name of the updated ad group criterion.
        /** @var AdGroupCriterion $updatedAdGroupCriterion */
        $updatedAdGroupCriterion = $response->getResults()[0];
        printf(
            "Updated ad group criterion with resource name: '%s'%s",
            $updatedAdGroupCriterion->getResourceName(),
            PHP_EOL
        );
    }

}