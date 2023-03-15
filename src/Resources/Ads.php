<?php

namespace GoogleAdsApiService\Resources;

use Google\Ads\GoogleAds\V12\Common\AdTextAsset;
use Google\Ads\GoogleAds\V12\Common\ExpandedTextAdInfo;
use Google\Ads\GoogleAds\V12\Common\ResponsiveSearchAdInfo;
use Google\Ads\GoogleAds\V12\Enums\AdGroupAdStatusEnum\AdGroupAdStatus;
use Google\Ads\GoogleAds\V12\Resources\Ad;
use Google\Ads\GoogleAds\V12\Resources\AdGroupAd;
use Google\Ads\GoogleAds\V12\Services\AdGroupAdOperation;
use Google\Ads\GoogleAds\V12\Services\AdOperation;

class Ads extends Blueprint\Resource
{
    /**
     * @param $customerId
     * @param $adGroupResource
     */
    public function __construct($client, $customerId)
    {
        parent::__construct($client, $customerId);
    }

    public function create(string $adGroupResource)
    {
        // Creates an ad and sets responsive search ad info.
        $ad = new Ad([
            'responsive_search_ad' => new ResponsiveSearchAdInfo([
                'headlines' => [
                    self::createAdTextAsset(
                        'Cruise to Mars'
                    ),
                    self::createAdTextAsset('Best Space Cruise Line'),
                    self::createAdTextAsset('Experience the Stars')
                ],
                'descriptions' => [
                    self::createAdTextAsset('Buy your tickets now'),
                    self::createAdTextAsset('Visit the Red Planet')
                ],
                'path1' => 'all-inclusive',
                'path2' => 'deals'
            ]),
            'final_urls' => ['http://www.example.com']
        ]);

        // Creates an ad group ad to hold the above ad.
        $adGroupAd = new AdGroupAd([
            'ad_group' => $adGroupResource,
            'status' => AdGroupAdStatus::PAUSED,
            'ad' => $ad
        ]);

        // Creates an ad group ad operation.
        $adGroupAdOperation = new AdGroupAdOperation();
        $adGroupAdOperation->setCreate($adGroupAd);

        // Issues a mutate request to add the ad group ad.
        $adGroupAdServiceClient = $this->getClient()->getAdGroupAdServiceClient();
        $response = $adGroupAdServiceClient->mutateAdGroupAds($this->getCustomerId(), [$adGroupAdOperation]);

        return $response->getResults()[0]->getResourceName();
    }

    /**
     * Update ads
     *
     * @return void
     */
    public function update(string $adResourceName)
    {
        // Construct the updated ad object.
        $updatedAd = new Ad([
            'resource_name' => $adResourceName,
            'final_urls' => ['https://www.example.com'],
            'expanded_text_ad' => new ExpandedTextAdInfo([
                'headline_part1' => 'Updated Headline 1',
                'headline_part2' => 'Updated Headline 2',
                'description' => 'Updated Description',
            ]),
        ]);

        // Create an array of operations to be executed (in this case, a single update operation).
        $updateOperations = [new AdOperation(['update' => $updatedAd])];
        $adServiceClient = $this->getClient()->getAdServiceClient();
        // Update the ad using the AdServiceClient's mutateAds method.
        var_dump($adServiceClient);
        $response = $adServiceClient->mutateAds($this->getCustomerId(), $updateOperations);

        return $response->getResults()[0]->getResourceName();
    }

    /**
     * Creates an ad text asset with the specified text and pin field enum value.
     *
     * @param string $text the text to be set
     * @param int|null $pinField the enum value of the pin field
     * @return AdTextAsset the created ad text asset
     */
    private static function createAdTextAsset(string $text, int $pinField = null): AdTextAsset
    {
        $adTextAsset = new AdTextAsset(['text' => $text]);
        if (!is_null($pinField)) {
            $adTextAsset->setPinnedField($pinField);
        }
        return $adTextAsset;
    }
}