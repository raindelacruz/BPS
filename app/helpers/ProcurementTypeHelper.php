<?php

namespace App\Helpers;

class ProcurementTypeHelper
{
    public const TYPES = [
        'competitive_bidding' => 'Competitive Bidding',
        'limited_source_bidding' => 'Limited Source Bidding',
        'competitive_dialogue' => 'Competitive Dialogue',
        'unsolicited_offer_with_bid_matching' => 'Unsolicited Offer with Bid Matching',
        'direct_contracting' => 'Direct Contracting',
        'direct_acquisition' => 'Direct Acquisition',
        'repeat_order' => 'Repeat Order',
        'small_value_procurement' => 'Small Value Procurement',
        'direct_sales' => 'Direct Sales',
        'direct_procurement_for_science_technology_and_innovation' => 'Direct Procurement for Science, Technology, and Innovation',
        'procurement_of_agricultural_and_fishery_products' => 'Procurement of Agricultural and Fishery Products',
        'negotiated_procurement' => 'Negotiated Procurement',
    ];

    public static function all(): array
    {
        return self::TYPES;
    }

    public static function values(): array
    {
        return array_keys(self::TYPES);
    }

    public static function label(string $value): string
    {
        return self::TYPES[$value] ?? $value;
    }
}
