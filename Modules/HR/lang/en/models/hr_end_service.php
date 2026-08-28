<?php

return [
    'singular' => 'End Service',
    'plural' => 'End Services',
    'fields' => [
        // Added automatically
        'add' => 'Add',

        'id' => 'Id',
        'name' => 'Name',
        'employee' => 'Employee',
        'end_date' => 'End Date',
        'status' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'description' => 'Description',
        'reason' => 'Reason',
        'reward_amount' => 'End of Service Benefits',
        'approved' => 'Approved',
        'duration' => 'duration',
        'total_penalties' => 'Total Outstanding Penalties',
        'total_advances' => 'Total Unpaid Advances',
        'total_deducts' => 'Unpaid Deductions',
        'net_reward' => 'Net End of Service Reward (Settlement)',
    ],

    'reasonList' => [
        'termination_duration_end' => 'Contract Expiration',
        'termination_unlawful_termination' => 'Termination by Employer/Employee for Unlawful Reason',
        'termination_article_80' => 'Termination Under Article 80',
        'termination_force_majeure' => 'Contract End Due to Force Majeure',
        'termination_woman_post_delivery' => 'Termination by Female Employee Within Three Months Post-Delivery',
        'termination_woman_post_marriage' => 'Termination by Female Employee Within Six Months of Marriage',
        'termination_article_81' => 'Termination Under Article 81',
        'resignation' => 'Resignation',
        'termination_agreement' => 'Termination by Agreement',
        'termination_worker_disability' => 'Worker Disability',
        'termination_employer_death' => 'Employer’s Death',
        'termination_worker_death' => 'Worker’s Death',
        'termination_business_transfer' => 'Transfer of Business to New Owner',
        'termination_retirement' => 'Retirement',
        'termination_notice_article_75' => 'Termination Notice Under Article 75',
        'resignation_trial_period' => 'Resignation During Probation Period',
    ],

    'employee_messages' => [
        'has_penalties' => 'The employee has penalties associated with them.',
        'has_advances' => 'The employee has advances associated with them.',
        'has_rewards' => 'The employee has rewards associated with them.',
        'has_commitments' => 'The employee has Custodies associated with them.',
        'has_tasks' => 'The employee has tasks associated with them.',
        'employee_data_unavailable' => 'Employee data or joining date is not available',
        'cannot_create_has_custodies' => 'Cannot create end of service: The employee has unreturned assets/custodies.',
    ],
];
