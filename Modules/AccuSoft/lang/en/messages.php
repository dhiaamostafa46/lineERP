<?php

return [

    'retrieved' => ':model retrieved successfully.',
    'saved'     => ':model saved successfully.',
    'updated'   => ':model updated successfully.',
    'deleted'   => ':model deleted successfully.',
    'not_found' => ':model not found',

    // Assets
    'asset_depreciation_success' => 'Asset depreciation created successfully and journal entry generated.',
    'asset_depreciation_failed' => 'Failed to depreciate asset. The asset may be fully depreciated or its salvage value prevents this.',
    'error_prefix' => 'Error: ',
    'asset_disposal_success' => 'Asset disposed successfully and necessary entries generated.',
    'depreciation_run_closed' => 'Depreciation for this month is already closed.',
    'depreciation_run_success' => 'Batch depreciation run created successfully with value :value',
    'depreciate_asset' => 'Depreciate Asset',
    'depreciation_date' => 'Depreciation Date',
    'depreciation_note' => 'The depreciation value will be calculated and a journal entry will be created automatically.',
    'execute_depreciation' => 'Execute Depreciation',
    'depreciate' => 'Depreciate',
    'cannot_delete_category_has_assets' => 'Cannot delete this category because it has linked fixed assets.',

    'dispose_asset_title' => 'Dispose / Scrap Fixed Asset',
    'dispose_asset_subtitle' => 'Close asset :name in accounting books and generate disposal entries.',
    'accounting_warning' => 'Accounting Warning',
    'dispose_warning_message' => 'This action will clear the historical cost and accumulated depreciation of the asset in the balance sheet, calculate capital gains or losses, and issue an approved journal entry automatically. This step cannot be undone!',
    'confirm_dispose' => 'Confirm Disposal',
    'manual_depreciation_title' => 'Manual Asset Depreciation',
    'manual_depreciation_subtitle' => 'Record manual depreciation amount for asset :name',
    'record_depreciation' => 'Record Depreciation',
];
