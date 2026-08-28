<?php return [
    'singular' => 'Advance',
    'plural' => 'Advances',
    'fields' => [
        'id' => 'ID',
        'employee_id' => 'Employee',
        'approver_id' => 'Approver',
        'payroll_id' => 'Payroll',
        'description' => 'Description',
        'reason' => 'Reason',
        'due_at' => 'Due Date',
        'approved_id' => 'Approved By',
        'approved_at' => 'Approval Date',
        'status' => 'Status',
        'amount' => 'Amount',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'attachment' => 'Attachment',
        'current_attachment' => 'Current Attachment',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'advance_details' => 'Advance Details',
    ],

    'select_employee' => 'Select Employee',
    'view_attachment' => 'View Attachment',
    'action' => 'Action',
    'approver' => 'Approver',

    // Installments
    'minimum_one_installment_required' => 'At least one monthly installment is required.',
    'invalid_month_format' => 'Invalid month format: :month. The required format is YYYY-MM.',
    'invalid_amount_for_month' => 'Invalid amount for month: :month.',
    'monthly_payments_total_mismatch' => 'The total of monthly installments does not match the advance amount. Expected: :expected, Actual: :actual, Difference: :difference.',

    // Errors
    'error_creating_advance' => 'An error occurred while creating the advance.',
    'error_updating_advance' => 'An error occurred while updating the advance.',
    'cannot_delete_paid_installment' => 'A paid installment cannot be deleted.',
    'advance_not_found' => 'Advance not found.',

    // Success messages
    'advance_created_successfully' => 'Advance created successfully.',
    'advance_updated_successfully' => 'Advance updated successfully.',
    'advance_deleted_successfully' => 'Advance deleted successfully.',

    // Warnings
    'cannot_modify_paid_payments' => 'Paid installments cannot be modified.',
    'some_payments_already_paid' => 'Some installments have already been paid and will not be modified.',

    // Display
    'total' => 'Total',
    'paid' => 'Paid',
    'pending' => 'Pending',
    'payment_progress' => 'Payment Progress',
    'no_monthly_payments' => 'No monthly installments available.',
    'advance_details' => 'Advance Details',
    'installment_details' => 'Installment Details',
];
