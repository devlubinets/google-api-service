### Google Ads api service

## Requirements

* Developer Token (for obtain developer token need create Google Cloud Console project)
* Customer Id (The client customer ID is the account number of the Google Ads client account you want to manage with the API, usually in the form 123-456-7890)

In process update ad and keyword I got next error: 
````shell
Fatal error: Uncaught Google\ApiCore\ApiException: {
    "message": "Request contains an invalid argument.",
    "code": 3,
    "status": "INVALID_ARGUMENT",
    "details": [
        {
            "@type": "google.ads.googleads.v12.errors.googleadsfailure-bin",
            "data": "<Unknown Binary Data>"
        },
        {
            "@type": "grpc-status-details-bin",
            "data": "<Unknown Binary Data>"
        },
        {
            "@type": "request-id",
            "data": "OxGwyV2hrO1MwniGYThTFw"
        }
    ]
}

  thrown in /app/vendor/googleads/google-ads-php/src/Google/Ads/GoogleAds/Lib/V12/GoogleAdsExceptionTrait.php on line 75

Google\Ads\GoogleAds\Lib\V12\GoogleAdsException: {
    "message": "Request contains an invalid argument.",
    "code": 3,
    "status": "INVALID_ARGUMENT",
    "details": [
        {
            "@type": "google.ads.googleads.v12.errors.googleadsfailure-bin",
            "data": "<Unknown Binary Data>"
        },
        {
            "@type": "grpc-status-details-bin",
            "data": "<Unknown Binary Data>"
        },
        {
            "@type": "request-id",
            "data": "OxGwyV2hrO1MwniGYThTFw"
        }
    ]
} in /app/vendor/googleads/google-ads-php/src/Google/Ads/GoogleAds/Lib/V12/GoogleAdsExceptionTrait.php on line 75

Call Stack:
    0.0043     403256   1. {main}() /app/index.php:0
    2.5059    6074080   2. GoogleAdsApiService\Resources\Keyword->update() /app/index.php:88
    2.5130    6467040   3. Google\Ads\GoogleAds\V12\Services\AdGroupCriterionServiceClient->mutateAdGroupCriteria() /app/src/Resources/Keyword.php:96
    2.5136    6479568   4. GuzzleHttp\Promise\Promise->wait() /app/vendor/googleads/google-ads-php/src/Google/Ads/GoogleAds/V12/Services/Gapic/AdGroupCriterionServiceGapicClient.php:356

````