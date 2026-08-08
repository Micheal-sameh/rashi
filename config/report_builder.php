<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reportable tables whitelist
    |--------------------------------------------------------------------------
    |
    | ONLY these tables and columns may ever be queried by the report builder.
    | Never resolve a table or column name from user input without checking
    | it against this list first. Sensitive columns (passwords, tokens) are
    | deliberately never listed here, and tables that only hold auth/token
    | data (personal_access_tokens, refresh_tokens, fcm_tokens, password
    | reset tokens, permission tables) are excluded entirely.
    |
    */

    'tables' => [

        'users' => [
            'label' => 'Users',
            'columns' => [
                'id' => ['label' => 'User ID', 'type' => 'number'],
                'name' => ['label' => 'Name', 'type' => 'string'],
                'email' => ['label' => 'Email', 'type' => 'string'],
                'membership_code' => ['label' => 'Membership Code', 'type' => 'string'],
                'phone' => ['label' => 'Phone', 'type' => 'string'],
                'score' => ['label' => 'Score', 'type' => 'number'],
                'points' => ['label' => 'Points', 'type' => 'number'],
                'created_at' => ['label' => 'Join Date', 'type' => 'date'],
            ],
        ],

        'orders' => [
            'label' => 'Orders',
            'columns' => [
                'id' => ['label' => 'Order ID', 'type' => 'number'],
                'user_id' => ['label' => 'User ID', 'type' => 'number'],
                'servant_id' => ['label' => 'Fulfilled By (User ID)', 'type' => 'number'],
                'reward_id' => ['label' => 'Reward ID', 'type' => 'number'],
                'quantity' => ['label' => 'Quantity', 'type' => 'number'],
                'points' => ['label' => 'Points', 'type' => 'number'],
                'status' => ['label' => 'Status', 'type' => 'number'],
                'created_at' => ['label' => 'Ordered At', 'type' => 'date'],
            ],
        ],

        'competitions' => [
            'label' => 'Competitions',
            'columns' => [
                'id' => ['label' => 'Competition ID', 'type' => 'number'],
                'name' => ['label' => 'Name', 'type' => 'string'],
                'start_at' => ['label' => 'Start Date', 'type' => 'date'],
                'end_at' => ['label' => 'End Date', 'type' => 'date'],
                'status' => ['label' => 'Status', 'type' => 'number'],
                'created_at' => ['label' => 'Created At', 'type' => 'date'],
            ],
        ],

        'quizzes' => [
            'label' => 'Quizzes',
            'columns' => [
                'id' => ['label' => 'Quiz ID', 'type' => 'number'],
                'competition_id' => ['label' => 'Competition ID', 'type' => 'number'],
                'name' => ['label' => 'Name', 'type' => 'string'],
                'date' => ['label' => 'Date', 'type' => 'date'],
                'created_at' => ['label' => 'Created At', 'type' => 'date'],
            ],
        ],

        'quiz_questions' => [
            'label' => 'Quiz Questions',
            'columns' => [
                'id' => ['label' => 'Question ID', 'type' => 'number'],
                'quiz_id' => ['label' => 'Quiz ID', 'type' => 'number'],
                'question' => ['label' => 'Question', 'type' => 'string'],
                'points' => ['label' => 'Points', 'type' => 'number'],
            ],
        ],

        'question_answers' => [
            'label' => 'Question Answers',
            'columns' => [
                'id' => ['label' => 'Answer ID', 'type' => 'number'],
                'quiz_question_id' => ['label' => 'Question ID', 'type' => 'number'],
                'answer' => ['label' => 'Answer', 'type' => 'string'],
                'is_correct' => ['label' => 'Is Correct', 'type' => 'number'],
            ],
        ],

        'user_answers' => [
            'label' => 'User Answers',
            'columns' => [
                'id' => ['label' => 'ID', 'type' => 'number'],
                'user_id' => ['label' => 'User ID', 'type' => 'number'],
                'quiz_question_id' => ['label' => 'Question ID', 'type' => 'number'],
                'question_answer_id' => ['label' => 'Answer ID', 'type' => 'number'],
                'points' => ['label' => 'Points Earned', 'type' => 'number'],
                'created_at' => ['label' => 'Answered At', 'type' => 'date'],
            ],
        ],

        'bonuses_penalties' => [
            'label' => 'Bonuses & Penalties',
            'columns' => [
                'id' => ['label' => 'ID', 'type' => 'number'],
                'user_id' => ['label' => 'User ID', 'type' => 'number'],
                'type' => ['label' => 'Type', 'type' => 'number'],
                'points' => ['label' => 'Points', 'type' => 'number'],
                'reason' => ['label' => 'Reason', 'type' => 'string'],
                'status' => ['label' => 'Status', 'type' => 'number'],
                'approved_by' => ['label' => 'Approved By (User ID)', 'type' => 'number'],
                'created_by' => ['label' => 'Created By (User ID)', 'type' => 'number'],
                'created_at' => ['label' => 'Created At', 'type' => 'date'],
            ],
        ],

        'point_transfers' => [
            'label' => 'Point Transfers',
            'columns' => [
                'id' => ['label' => 'ID', 'type' => 'number'],
                'sender_id' => ['label' => 'Sender (User ID)', 'type' => 'number'],
                'receiver_id' => ['label' => 'Receiver (User ID)', 'type' => 'number'],
                'points' => ['label' => 'Points', 'type' => 'number'],
                'family_code' => ['label' => 'Family Code', 'type' => 'string'],
                'reason' => ['label' => 'Reason', 'type' => 'string'],
                'created_by' => ['label' => 'Created By (User ID)', 'type' => 'number'],
                'created_at' => ['label' => 'Created At', 'type' => 'date'],
            ],
        ],

        'groups' => [
            'label' => 'Groups',
            'columns' => [
                'id' => ['label' => 'Group ID', 'type' => 'number'],
                'name' => ['label' => 'Name', 'type' => 'string'],
                'abbreviation' => ['label' => 'Abbreviation', 'type' => 'string'],
            ],
        ],

        'rewards' => [
            'label' => 'Rewards',
            'columns' => [
                'id' => ['label' => 'Reward ID', 'type' => 'number'],
                'group_id' => ['label' => 'Group ID', 'type' => 'number'],
                'name' => ['label' => 'Name', 'type' => 'string'],
                'quantity' => ['label' => 'Quantity', 'type' => 'number'],
                'points' => ['label' => 'Points Cost', 'type' => 'number'],
                'status' => ['label' => 'Status', 'type' => 'number'],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed operators per column type
    |--------------------------------------------------------------------------
    */

    'operators' => [
        'string' => ['=', '!=', 'LIKE', 'NOT LIKE'],
        'number' => ['=', '!=', '>', '<', '>=', '<='],
        'date' => ['=', '!=', '>', '<', '>=', '<='],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default / max page size
    |--------------------------------------------------------------------------
    */

    'default_per_page' => 25,
    'max_per_page' => 100,

];
