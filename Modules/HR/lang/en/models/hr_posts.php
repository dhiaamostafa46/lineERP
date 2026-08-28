<?php

return [
    'singular' => 'Post',
    'plural' => 'Posts & Announcements',

    'fields' => [
        'id' => 'ID',
        'title' => 'Title',
        'body' => 'Content',
        'type' => 'Type',
        'status' => 'Status',
        'flage' => 'Audience',
        'employee_id' => 'Employees',
        'department_id' => 'Departments',
        'branch_id' => 'Branches',
        'published_at' => 'Publish Date',
        'expires_at' => 'Expiry Date',
        'is_pinned' => 'Pin to Top',
        'image' => 'Cover Image',
        'created_by' => 'Created By',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    'types' => [
        'news' => 'News',
        'announcement' => 'Announcement',
    ],

    'statuses' => [
        'draft' => 'Draft',
        'published' => 'Published',
    ],

    'flages' => [
        'all' => 'All Employees',
        'employees' => 'Selected Employees',
        'department' => 'Departments',
        'branches' => 'Branches',
    ],

    'feed_title' => 'News & Announcements',
    'no_posts' => 'No posts to display.',
    'pinned' => 'Pinned',
    'read_more' => 'Read more',
    'show_less' => 'Show less',
];
