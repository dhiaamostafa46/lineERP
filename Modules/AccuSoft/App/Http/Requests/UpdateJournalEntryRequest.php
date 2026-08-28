<?php

namespace Modules\AccuSoft\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'entry_date' => 'required|date',
            'entry_type' => 'required|integer',
            'source' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'description' => 'nullable|string|max:1000',
            'details' => 'required|array|min:2',
            'details.*.tree_account_id' => 'required|exists:tree_accounts,id',
            'details.*.cost_center_id' => 'nullable|exists:cost_centers,id',
            'details.*.debit' => 'nullable|numeric|min:0',
            'details.*.credit' => 'nullable|numeric|min:0',
            'details.*.description' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'details.required' => __('accusoft::models/as_journal_entries.messages.details_required'),
            'details.min' => __('accusoft::models/as_journal_entries.validations.min_two_rows'),
            'details.*.tree_account_id.required' => __('accusoft::models/as_journal_entries.validations.account_required_all_rows'),
            'details.*.tree_account_id.exists' => __('accusoft::models/as_journal_entries.validations.account_not_found'),
            'details.*.cost_center_id.exists' => __('accusoft::models/as_journal_entries.validations.cost_center_not_found'),
            'details.*.debit.numeric' => __('accusoft::models/as_journal_entries.validations.debit_numeric'),
            'details.*.credit.numeric' => __('accusoft::models/as_journal_entries.validations.credit_numeric'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateBalanceAndDetails($validator);
        });
    }

    /**
     * Validate balance and detail rules.
     */
    protected function validateBalanceAndDetails($validator): void
    {
        $details = $this->input('details', []);

        if (empty($details)) {
            return;
        }

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($details as $index => $detail) {
            $debit = (float) ($detail['debit'] ?? 0);
            $credit = (float) ($detail['credit'] ?? 0);

            if ($debit > 0 && $credit > 0) {
                $validator->errors()->add(
                    "details.{$index}.debit",
                    __('accusoft::models/as_journal_entries.messages.no_both_debit_credit')
                );
            }

            if ($debit == 0 && $credit == 0) {
                $validator->errors()->add(
                    "details.{$index}.debit",
                    __('accusoft::models/as_journal_entries.validations.amount_required')
                );
            }

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            $validator->errors()->add(
                'balance',
                sprintf(
                    __('accusoft::models/as_journal_entries.validations.unbalanced_detailed'),
                    number_format($totalDebit, 2),
                    number_format($totalCredit, 2)
                )
            );
        }

        if ($totalDebit == 0 || $totalCredit == 0) {
            $validator->errors()->add('balance', __('accusoft::models/as_journal_entries.validations.zero_total_error'));
        }

        // التحقق من أن الحسابات التي تتطلب مركز تكلفة تمتلك مركز تكلفة
        $accountIds = collect($details)->pluck('tree_account_id')->filter()->unique()->toArray();
        if (!empty($accountIds)) {
            $accounts = \App\Models\AccuSoft\TreeAccounts::whereIn('id', $accountIds)->get()->keyBy('id');
            foreach ($details as $index => $detail) {
                $accountId = $detail['tree_account_id'] ?? null;
                if ($accountId && isset($accounts[$accountId])) {
                    if ($accounts[$accountId]->use_cost_center) {
                        $costCenterId = $detail['cost_center_id'] ?? null;
                        if (empty($costCenterId)) {
                            $accountName = $accounts[$accountId]->name ?? 'الحساب';
                            $validator->errors()->add(
                                "details.{$index}.cost_center_id",
                                sprintf(__('accusoft::models/as_journal_entries.validations.cost_center_required_for_account'), $accountName)
                            );
                        }
                    }
                }
            }
        }
    }
}
