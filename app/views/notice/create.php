<?php

use App\Helpers\ResponseHelper;
use App\Helpers\ViewHelper;
?>
<div class="page-head">
    <div>
        <h1>New Procurement</h1>
        <p>Choose the procurement mode first so the system opens the correct creation form and workflow.</p>
    </div>
</div>

<div class="detail-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
    <div>
        <dt>Competitive Bidding</dt>
        <dd>Start with the Bid Notice details and posting schedule.</dd>
        <p class="helper-text" style="margin: 8px 0 0;">Use this path for Bid Notice posting, bid bulletin, resolution, award, contract, and notice to proceed.</p>
        <div class="btn-row" style="margin-top: 12px;">
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('procurements/create/competitive-bidding')); ?>">Create Competitive Bidding Posting</a>
        </div>
    </div>
    <div>
        <dt>Small Value Procurement</dt>
        <dd>Start with the SVP record, then continue through a clean document sequence.</dd>
        <p class="helper-text" style="margin: 8px 0 0;">Use this path for RFQ, Abstract of Quotations or Canvass, Award, and optional Contract or Purchase Order only.</p>
        <div class="btn-row" style="margin-top: 12px;">
            <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('procurements/create/svp')); ?>">Create Small Value Procurement Record</a>
        </div>
    </div>
</div>

<div class="btn-row" style="margin-top: 14px;">
    <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('dashboard')); ?>">Back to dashboard</a>
    <a class="btn-link" href="<?= ViewHelper::escape(ResponseHelper::url('notices')); ?>">View procurement records</a>
</div>
