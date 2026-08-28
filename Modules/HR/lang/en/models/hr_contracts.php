<?php

return [

    'singular' => 'Contract',
    'plural'   => 'Contracts',
    'qiwa'     => 'Qiwa',

    /*
    |--------------------------------------------------------------------------
    | Contract Fields
    |--------------------------------------------------------------------------
    */
    'fields' => [
        'id'                    => 'ID',
        'employee_id'           => 'Employee',
        'type_id'               => 'Type',
        'file'                  => 'File',
        'start_at'              => 'Start Date',
        'end_at'                => 'End Date',
        'qiwa_no'               => 'Is Contract in Qiwa',
        'qiwa'                  => 'Qiwa',
        'name'                  => 'Name',
        'status'                => 'Status',
        'created_at'            => 'Created At',
        'updated_at'            => 'Updated At',
        'add'                   => 'New Contract',

        'contract_number'       => 'Contract Number',
        'duration_months'       => 'Contract Duration (Months)',
        'auto_renewable'        => 'Auto Renewable',
        'signed_date'           => 'Signed Date',
        'accepted_by_employee'  => 'Employee Acceptance',
        'accepted_date'         => 'Acceptance Date',
        'approved_by_hr'        => 'HR Approval',
        'approved_date'         => 'Approval Date',
        'approved_by'           => 'Approved By',
        'company_signature'     => 'Company Signature',
        'employee_signature'    => 'Employee Signature',
        'signatory_company_id'  => 'Company Signatory',
        'signatory_employee_id' => 'Employee Signatory',
        'location'              => 'Work Location',
        'office'                => 'Office',
        'termination_terms'     => 'Termination Terms',
        'additional_data'       => 'Additional Data',
        'notes'                 => 'Notes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Statuses
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'draft'      => 'Draft',
        'active'     => 'Active',
        'expired'    => 'Expired',
        'terminated' => 'Terminated',
        'renewed'    => 'Renewed',
        'unknown'    => 'Unknown',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Actions
    |--------------------------------------------------------------------------
    */
    'actions' => [
        'sign'       => 'Sign Contract',
        'approve'    => 'Approve Contract',
        'reject'     => 'Reject Contract',
        'renew'      => 'Renew Contract',
        'terminate'  => 'Terminate Contract',
        'download'   => 'Download Contract',
        'preview'    => 'Preview Contract',
    ],

    /*
    |--------------------------------------------------------------------------
    | Approval & Acceptance
    |--------------------------------------------------------------------------
    */
    'approval' => [
        'employee_acceptance'  => 'I, the second party, acknowledge and agree to all terms of this contract.',
        'hr_approval'          => 'This contract has been approved by the Human Resources Department.',
        'electronic_signature' => 'This contract is electronically signed and does not require a handwritten signature.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Renewal & Termination
    |--------------------------------------------------------------------------
    */
    'renewal_and_termination' => [
        'auto_renewal_enabled'  => 'This contract is automatically renewable.',
        'auto_renewal_disabled' => 'This contract is not automatically renewable.',
        'termination_notice'    => 'Termination Notice',
        'termination_reason'    => 'Termination Reason',
    ],

    /*
    |--------------------------------------------------------------------------
    | Misc
    |--------------------------------------------------------------------------
    */
    'misc' => [
        'yes'   => 'Yes',
        'no'    => 'No',
        'day'   => 'Day',
        'month' => 'Month',
        'year'  => 'Year',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Header
    |--------------------------------------------------------------------------
    */
    'contracts' => [
        'contract_number' => 'Contract Number',
        'contract_text'   => 'This contract has been concluded electronically in',
        'saudi_arabia'    => 'Kingdom of Saudi Arabia',
        'on_day'          => 'On',
        'm'               => 'AD',
    ],

    'employment_contract' => 'Employment Contract',

    /*
    |--------------------------------------------------------------------------
    | First Party
    |--------------------------------------------------------------------------
    */
    'first_party' => 'First Party',
    'first_party_table' => [
        'company_name'             => 'Company / Establishment',
        'unified_national_number'  => 'Unified National Number',
        'facility_number'          => 'Facility Number',
        'commercial_register'      => 'Commercial Registration',
        'address'                  => 'Address',
        'workplace'                => 'Workplace',
        'email'                    => 'Email',
        'represented_by'           => 'Represented By',
        'date'                     => 'Date',
        'contract_signed'          => 'Contract Signed',
        'organization_name'        => 'Organization Name',
        'user_name'                => 'User Name',
        'general_manager'          => 'General Manager',
        'signed_on'                => 'Signed On',
        'development_team'         => 'Hereinafter referred to as (First Party)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Second Party
    |--------------------------------------------------------------------------
    */
    'second_party' => 'Second Party',
    'second_party_table' => [
        'name'              => 'Name',
        'job'               => 'Job Title',
        'employee_number'   => 'Employee Number',
        'nationality'       => 'Nationality',
        'birth_date'        => 'Date of Birth',
        'id_number'         => 'ID Number',
        'id_type'           => 'ID Type',
        'gender'            => 'Gender',
        'religion'          => 'Religion',
        'end_date'          => 'End Date',
        'marital_status'    => 'Marital Status',
        'education_level'   => 'Education Level',
        'specialization'    => 'Specialization',
        'iban_number'       => 'IBAN',
        'email'             => 'Email',
        'phone_number'      => 'Mobile Number',
        'second_party_reference' => 'Hereinafter referred to as (Second Party)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Details
    |--------------------------------------------------------------------------
    */
    'contract_details' => [
        'agreement' => [
            'part_1' => 'The two parties agreed that the second party shall work for the first party under its supervision as',
            'part_2' => 'and perform the assigned duties in accordance with their professional and technical capabilities and in line with Articles (58, 59, 60) of the Saudi Labor Law.',
        ],
        'duration' => [
            'main'       => 'This contract duration',
            'start'      => 'starts from',
            'end'        => 'and ends on',
            'duration_2' => 'The employee start date is',
        ],
        'renewal' => [
            'main'       => 'This contract shall be renewed for similar periods unless either party notifies the other in writing',
            'renewal_2'  => 'days prior to the contract expiration.',
        ],
        'probation_period' => [
            'main'              => 'The second party shall be subject to a probation period of',
            'probation_period_2'=> 'days starting from the work commencement date.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Work Details
    |--------------------------------------------------------------------------
    */
    'work_details' => [
        'work_days_and_hours' => 'Work Days and Hours',
        'normal_work_days'    => 'Normal working days are',
        'work_days_count'     => '6 days',
        'work_hours'          => 'per week with working hours',
        'daily_hours'         => 'per day',
        'overtime_clause'     => 'Overtime shall be compensated with an additional 50% of the basic hourly wage.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Termination
    |--------------------------------------------------------------------------
    */
    'contract_termination' => [
        'heading'               => 'Contract Termination',
        'termination_clause'    => 'This contract expires upon the end of its term or by mutual agreement.',
        'first_party_rights'    => 'The first party may terminate the contract according to Article (80) of the Labor Law.',
        'second_party_rights'   => 'The second party may terminate the contract according to Article (81) of the Labor Law.',
    ],

    /*
    |--------------------------------------------------------------------------
    | End of Service
    |--------------------------------------------------------------------------
    */
    'end_of_service_bonus' => [
        'heading'     => 'End of Service Gratuity',
        'description' => 'The employee is entitled to an end-of-service benefit in accordance with Saudi Labor Law.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Law & Jurisdiction
    |--------------------------------------------------------------------------
    */
    'applicable_law_and_jurisdiction' => [
        'heading'            => 'Applicable Law and Jurisdiction',
        'contract_subject'   => 'This contract shall be governed by Saudi Labor Law.',
        'dispute_resolution' => 'Labor courts in the Kingdom of Saudi Arabia shall have jurisdiction.',
        'notifications'      => 'All notifications shall be conducted electronically via the Evix platform.',
        'contract_export'    => 'This contract is issued electronically and accessible through the Evix platform.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contract Parties
    |--------------------------------------------------------------------------
    */
    'contract_parties' => [
        'first_party'            => 'First Party',
        'first_party_company'    => 'Company / Establishment:',
        'first_party_name'       => 'Name:',
        'second_party'           => 'Second Party',
        'second_party_employee'  => 'Employee:',
    ],


    // Added automatically group
    'commitments_details' => [
        'first_party_commitments' => 'First Party Commitments',
        'basic_salary' => [
            'main' => 'Main',
            'currency' => 'Currency',
            'month' => 'Month',
        ],
        'allowances' => [
            'commitment' => 'Commitment',
            'payment' => 'Payment',
            'currency' => 'Currency',
            'month' => 'Month',
        ],
        'annual_leave' => [
            'main' => 'Main',
            'days' => 'Days',
        ],
        'medical_care' => 'Medical Care',
        'social_insurance' => 'Social Insurance',
        'recruitment_fees' => [
            'main' => 'Main',
        ],
        'funeral_expenses' => [
            'main' => 'Main',
        ],
        'second_party_commitments' => 'Second Party Commitments',
        'work_execution' => 'Work Execution',
        'tool_care' => 'Tool Care',
        'assistance' => 'Assistance',
        'medical_exams' => 'Medical Exams',
        'conduct' => 'Conduct',
        'social_insurance_contribution' => 'Social Insurance Contribution',
    ],
];

