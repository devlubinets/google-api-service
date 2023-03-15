<?php

namespace GoogleAdsApiService;

use Google\Ads\GoogleAds\V12\Enums\BudgetDeliveryMethodEnum\BudgetDeliveryMethod;
use GoogleAdsApiService\Resources\Ads;
use GoogleAdsApiService\Resources\Campaign;
use GoogleAdsApiService\Resources\Group;
use GoogleAdsApiService\Resources\Keyword;
use GoogleAdsApiService\Resources\Report;

require __DIR__ . '/vendor/autoload.php';

//phpinfo();

$adGroup = '146054581653';

$config['loginCustomerId'] = '5692699346';
$config['customerId'] = '1024228948';
$config['developerToken'] = 'hEX_R_Tc5s_MsxQ0RQ7Arg';
$config['clientRefreshToken'] = '1//03Id97FQvoTXsCgYIARAAGAMSNwF-L9IrKgE0-sSz4Quv58ycTsFIdbf_hQgjfdZyiMYaUawRzwshGWjh5t6hwDvXVQrSkMGJ7X0';
$config['clientSecret'] = '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';
$config['clientId'] = '316490464826-bi0ood44qedkugdt56u16vdlb22h6mss.apps.googleusercontent.com';

$s = new GoogleAdsApiService($config);

$campaignResource = new Campaign($s->getGoogleClient(), $s->getCustomerId());

$paramsForCustomUrl = [
    [
        'key' => 'site',
        'value' => 'google'
    ],
    [
        'key' => 'type',
        'value' => 'search'
    ],
];

$paramsForNetwork = [
    'target_google_search' => true,
    'target_search_network' => true,
    'target_content_network' => true,
    'target_partner_search_network' => false,
];

$paramsForNewCompany = [
    'name' => 'Test ' . rand(777, 123123),
    'target_google_search' => true,
    'target_search_network' => true,
    'target_content_network' => true,
    'target_partner_search_network' => false,
];
$paramsForBudget = [
    'name' => 'test budget' . rand(878, 222222),
    'delivery_method' => BudgetDeliveryMethod::STANDARD,
    'amount_micros' => 10000,
];

$newCampaign = $campaignResource->withParams(
    $paramsForNewCompany,
    $paramsForBudget,
    $paramsForNetwork,
    $paramsForCustomUrl)->create($paramsForNewCompany, $paramsForBudget);
var_dump($newCampaign);

$groupResource = new Group($s->getGoogleClient(), $s->getCustomerId());
$group = $groupResource->create('Test group' . rand(999,99999), $newCampaign);
var_dump($group);

$adsResource = new Ads($s->getGoogleClient(), $s->getCustomerId());
$ad = $adsResource->create($group);
var_dump($ad);

$adsKeywordResource = new Keyword($s->getGoogleClient(), $s->getCustomerId());
$keyword = $adsKeywordResource->create('electrical motor', $group);
var_dump($keyword);

//update Campaign
$campaignResource->update($newCampaign);
//update Group
$groupResource->update($group, 20000);

//update Keyword todo doesn't work
$cus = '1024228948';
$g = '146595614323';
$c = '146595614323~303183403678';
$adsKeywordResource->update($cus, $g, $c);
//update Ads todo doesn't work
$adsResource->update($ad);


// Statistic
//$reports = new Report($s->getGoogleClient(), $s->getCustomerId());
//var_dump($reports->getCampaignReport());
//var_dump($reports->getCustomerReport());
//var_dump($reports->getGroupReport($newCampaign));
//$reports->getAdReport('648809678391');
