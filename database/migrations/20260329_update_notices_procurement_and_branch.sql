ALTER TABLE `notices`
    ADD COLUMN `branch` VARCHAR(100) NULL AFTER `region`;

UPDATE `notices`
SET `branch` = NULL
WHERE `branch` = '';

UPDATE `notices`
SET `procurement_type` = 'competitive_bidding'
WHERE `procurement_type` = 'public_bidding';

UPDATE `notices`
SET `procurement_type` = 'direct_acquisition'
WHERE `procurement_type` = 'shopping';

UPDATE `notices`
SET `procurement_type` = 'small_value_procurement'
WHERE `procurement_type` = 'small_value';

UPDATE `notices`
SET `procurement_type` = 'negotiated_procurement'
WHERE `procurement_type` IN ('agency_to_agency', 'pol', 'emergency', 'leased_property');

ALTER TABLE `notices`
    MODIFY COLUMN `procurement_type` ENUM(
        'competitive_bidding',
        'limited_source_bidding',
        'competitive_dialogue',
        'unsolicited_offer_with_bid_matching',
        'direct_contracting',
        'direct_acquisition',
        'repeat_order',
        'small_value_procurement',
        'direct_sales',
        'direct_procurement_for_science_technology_and_innovation',
        'procurement_of_agricultural_and_fishery_products',
        'negotiated_procurement'
    ) NOT NULL;
