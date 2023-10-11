<?php

return [
    'admin'         => [
        'email'       => 'admin_harrier@yopmail.com',
        'password'     => 'Admin@123',
    ],
    'emptyData'         => new \stdClass(),
    'validResponse'     => [
        'success'    => true,
        'statusCode' => 200,
    ],
    'invalidResponse'   => [
        'success'    => false,
        'statusCode' => 400,
    ],
    'invalidToken'      => [
        'success'    => false,
        'statusCode' => 401,
        'message'    => 'Unauthorized User!',
    ],

    'role' => [
        'admin' => [ 'value' => 'ADMIN', 'name' => 'Harrier'],
        'guest' => [ 'value' => 'GUEST', 'name' => 'Guest'],
        'emp'   => [ 'value' => 'EMP', 'name' => 'Employer', 'is_pe' => 'PE:1', 'other_name' => 'Prospective Employer'],
    ],

    'prefix_names' => 'admin|guest|emp',

    'is_active'         => [
        'notActive' => 0,
        'active'    => 1,
    ],

    'is_block'          => [
        'notBlock' => 0,
        'block'    => 1,
    ],

    'login_request' => [
        'requested'=>   [ 'value' => 1, 'name' => 'Requested'],
        'expired' =>   [ 'value' => 2, 'name' => 'Expired'],
        'active' =>   [ 'value' => 3, 'name' => 'Active'],
    ],
    /*
        all tables => status ( 0=Inactive, 1=Active	)
    */
    'status'          => [
        'inactive' => 0,
        'active'    => 1,
    ],
    /*
        -candidates,
        qualified_lawyer,
        jurisdiction,
        legaltech_vendor_or_consultancy,
        is_job_search,
        freelance_current,
        freelance_future,
        law_degree,
        is_pe,
        is_request,
        is_login  =>  ( 	0=No, 1=Yes )
    */
    'is_job_search' => [
        'no' => 0,
        'yes'    => 1,
    ],
    
    /*
       -job_candidates => is_cv (   1=requested, 2=accepted, 3=rejected )
    */
    'is_cv' => [
        'requested'=>   [ 'value' => 1, 'name' => 'Requested'],
        'accepted' =>   [ 'value' => 2, 'name' => 'Accepted'],
        'rejected' =>   [ 'value' => 3, 'name' => 'Rejected'],
    ],
    'interview_request' => [
        'requested'=>   [ 'value' => 1, 'name' => 'Requested'],
        'accepted' =>   [ 'value' => 2, 'name' => 'Accepted'],
        'rejected' =>   [ 'value' => 3, 'name' => 'Rejected'],
    ],
    
    /* 
        -candidates => status , 
        -job_candidates => c_job_status (   1=Active, 2=Passive, 3=Very Passive, 4=Closed   )
    */
    'is_candidate_status' => [
        'active'=>          [ 'value' => 1, 'name' => 'Active'],
        'passive' =>        [ 'value' => 2, 'name' => 'Passive'],
        'very_passive' =>   [ 'value' => 3, 'name' => 'Very Passive'],
        'closed' =>         [ 'value' => 4, 'name' => 'Closed'],
    ],

    /*
        -working_arrangements,
        working_schedule => 	1=Fulltime office, 2=Fulltime remote, 3=Hybrid	 
    */
    'is_working' => [
        'office'=>   [ 'value' => 1, 'name' => 'Fulltime office'],
        'remote' =>   [ 'value' => 2, 'name' => 'Fulltime remote'],
        'hybrid' =>   [ 'value' => 3, 'name' => 'Hybrid'],
    ],

    'notification_type' => [
        'req_log'       => [ "key" => "request_login", "title" => "Login", "message" => "Login request sent" ],
        'log_accept'    => [ "key" => "login_accept", "title" => "Accepted", "message" => "Login accepted" ],
        'job_int'       => [ "key" => "job_interest", "title" => "Interest", "message" => "Job interested" ],
        'cv_req'       => [ "key" => "cv_requested", "title" => "CV Requested", "message" => "CV Requested" ],
        'cv_acc'       => [ "key" => "cv_accepted", "title" => "CV Accepted", "message" => "CV Accepted" ],
        'cv_rej'       => [ "key" => "cv_rejected", "title" => "CV Rejected", "message" => "CV Rejected" ],
        'cv_update'       => [ "key" => "cv_updated", "title" => "CV Updated", "message" => "Candidate Profile Updated" ],
        'interview_req'       => [ "key" => "interview_requested", "title" => "Interview Requested", "message" => "Interview Requested" ],
        'interview_acc'       => [ "key" => "interview_accepted", "title" => "Interview Accepted", "message" => "Interview Accepted" ],
    ],

    'types' => [
        'role' => [
            'name' => 'role',
            'titles' =>  ['ADMIN', 'GUEST', 'EMP']
        ],
        'employer_type' => [
            'name' => 'employer_type',
            'titles' => ['Law Firm', 'Corporate/In-house', 'Tech Vendor', 'ALSP', 'Consultancy', 'Other']
        ],
        'region' => [
            'name' => 'region',
            'titles' => [
                'Northern Ireland', 'Scotland', 'Wales', 'England – North East', 'England – North West', 'England – Yorkshire & Humber',
                'England – East Midlands', 'England – West Midlands', 'England – East Anglia', 'England – South East', 'England – South West', 'England – London'
            ]
        ],
        'working_arrangements' => [
            'name' => 'working_arrangements',
            'titles' => ['Full time - Office', 'Full time - Remote', 'Full time - Hybrid', 'Part time - Office', 'Part time - Remote', 'Part time - Hybrid',]
        ],
        'customer_type' => [
            'name' => 'customer_type',
            'titles' => ['B2C', 'Corporates', 'Law Firms']
        ],
        'legal_tech_tools' => [
            'name' => 'legal_tech_tools',
            'titles' => [
                'Contract Express - Beginner',
                'Contract Express - Competent',
                'Contract Express - Expert',
                'HighQ - Beginner',
                'HighQ - Competent',
                'HighQ - Expert',
                'BRYTER - Beginner',
                'BRYTER - Competent',
                'BRYTER - Expert',
                'Neota - Beginner',
                'Neota - Competent',
                'Neota - Expert',
                'Luminance - Beginner',
                'Luminance - Competent',
                'Luminance - Expert',
                'Kira - Beginner',
                'Kira - Competent',
                'Kira - Expert',
                'iManage - Beginner',
                'iManage - Competent',
                'iManage - Expert',
                'Juro - Beginner',
                'Juro - Competent',
                'Juro - Expert',
                'Ironclad - Beginner',
                'Ironclad - Competent',
                'Ironclad - Expert',
                'Agiloft - Beginner',
                'Agiloft - Competent',
                'Agiloft - Expert',
                'Not Applicable',
            ]
        ],
        'tech_tools' => [
            'name' => 'tech_tools',
            'titles' => [
                'HTML - Beginner',
                'HTML - Competent',
                'HTML - Expert',
                'Python - Beginner',
                'Python - Competent',
                'Python - Expert',
                'CSS - Beginner',
                'CSS - Competent',
                'CSS - Expert',
                'Microsoft Excel - Beginner',
                'Microsoft Excel - Competent',
                'Microsoft Excel - Expert',
                'JIRA - Beginner',
                'JIRA - Competent',
                'JIRA - Expert',
                'Salesforce - Beginner',
                'Salesforce - Competent',
                'Salesforce - Expert',
                'Microsoft Power Automate - Beginner',
                'Microsoft Power Automate - Competent',
                'Microsoft Power Automate - Expert',
                'Microsoft Power BI - Beginner',
                'Microsoft Power BI - Competent',
               'Microsoft Power BI - Expert',
                'Trello - Beginner',
            ]
        ],
        'languages' => [
            'name' => 'languages',
            'titles' => ['English', 'French', 'Spanish']
        ],
        'sex' => [
            'name' => 'sex',
            'titles' => ['Male', 'Female', 'Intersex', 'Non-binary', 'Other', 'Prefer not to say']
        ],
        'gender' => [
            'name' => 'gender',
            'titles' => ['Male', 'Female', 'Intersex', 'Non-binary', 'Other', 'Prefer not to say']
        ],
        'channel' => [
            'name' => 'channel',
            'titles' => ['LinkedIn', 'Google', 'Harrier contacted me directly', 'Personal referral', 'Event (please specify)', 'Online advert (please specify)', 'Other (please specify)']
        ],
        'cultural_background' => [
            'name' => 'cultural_background',
            'titles' => [
                'Asian or Asian British: Indian',
                'Asian or Asian British: Pakistani',
                'Asian or Asian British: Bangladeshi',
                'Arab',
                'Black, African, Caribbean or Black British: African',
                'Black, African, Caribbean or Black British: Caribbean',
                'Mixed or Multiple Ethnic Groups: Asian and White',
                'Mixed or Multiple Ethnic Groups: Black African and White',
                'Mixed or Multiple Ethnic Groups: Black Caribbean and White',
                'White: English',
                'White: Welsh',
                'White: Scottish',
                'White: Northern Irish',
                'White: British',
                'White: Gypsy or Irish Traveller',
                'White: European',
                'White: Other (e.g. American, Australian, South African)',
                'Other',
                'Prefer not to say',
            ]
        ],
        'faith' => [
            'name' => 'faith',
            'titles' => ['No religion or belief', 'Buddhist', 'Christian', 'Hindu', 'Jewish', 'Muslim', 'Sikh', 'Other', 'Prefer not to say']
        ],
        'qualifications' => [
            'name' => 'qualifications',
            'titles' => [
                'LPC',
                'GDL',
                'LLB',
                'LLM',
                'JD',
                'Scrum Master',
                'PRINCE2 Practitioner',
                'PRINCE2 Foundation',
                'PRINCE2 Foundation & Practitioner',
                'MBA',
                'Relativity Certified Administrator',
                'Relativity Review Management Specialist',
                'Relativity Project Management Specialist',
                'RelativityOne Certified Pro',
                'PhD',
                'N/A'
            ]
        ],
        'candidate_job_status' => [
            'name' => 'candidate_job_status',
            'titles' => ['Offer Accepted', 'Offer Rejected', '1st Interview Held', '2nd Interview Held', '3rd Interview Held', 'Candidate withdrawn', 'Candidate rejected']
        ],
        'sexual_orientation' => [
            'name' => 'sexual_orientation',
            'titles' => ['Heterosexual', 'Gay', 'Lesbian', 'Bisexual', 'Asexual', 'Undecided', 'Prefer not to say']
        ],
        'candidate_status' => [
            'name' => 'candidate_status',
            'titles' => ['Active (actively looking for a new role)', 'Passive (open to receiving CV requests from Employers)', 'Very Passive (unlikely to accept CV requests unless the role is exceptional)', 'Closed (no intention of leaving current employer)']
        ],
        'main_earner_occupations' => [
            'name' => 'mst_main_earner_occupations',
            'titles' => [
                'Modern professional and traditional professional occupations such as teacher, nurse, physiotherapist, social worker, musician, police officer (sergeant or above), software designer, accountant, solicitor, medical practitioner, scientist, civil/mechanical engineer. ',
                'Senior, middle or junior managers or administrators such as finance manager, chief executive, large business owner, office manager, retail manager, bank manager, restaurant manager, warehouse manager.',
                'Clerical and intermediate occupations such as secretary, personal assistant, call centre agent, clerical worker, nursery nurse.',
                'Technical and craft occupations such as motor mechanic, plumber, printer, electrician, gardener, train driver.',
                'Routine, semi-routine manual and service occupations such as postal worker, machine operative, security guard, caretaker, farm worker, catering assistant, sales assistant, HGV driver, cleaner, porter, packer, labourer, waiter/waitress, bar staff.',
                'Long-term unemployed (claimed Jobseeker’s Allowance or earlier unemployment benefit for more than a year).',
                'Small business owners who employed fewer than 20 people such as corner shop owners, small plumbing companies, retail shop owner, single restaurant or cafe owner, taxi owner, garage owner.',
                'Other such as retired, this question does not apply to me, I don’t know.',
                'I prefer not to say.'
            ]
        ],
        'languages' => [
            'name' => 'mst_languages',
            'titles' => ['English', 'French', 'German', 'Spanish', 'Portuguese', 'Hindi', 'Danish', 'Norwegian', 'Swedish', 'Finnish', 'Italian', 'Mandarin', 'Cantonese', 'Russian', 'Arabic', 'Dutch', 'Polish']
        ],
        'mst_school_types' => [
            'name' => 'mst_school_types',
            'titles' => [
                'Prefer not to say',
                'State-run or state-funded school',
                'Attended school outside the UK',
                'Independent or fee-paying school',
                'Independent or fee-paying school, where I received a means-tested bursary covering 90% or more of the overall cost of attending throughout my time there',
                'I don’t know'
            ]
        ],
    ],
    
    'master_tables' => ['mst_candidate_job_statuses','mst_employer_types','mst_regions','mst_working_arrangements', 'mst_customer_types',
        'mst_legal_tech_tools','mst_tech_tools','mst_languages','mst_sexes','mst_genders','mst_channels',
        'mst_cultural_backgrounds','mst_candidate_statuses','mst_faiths','mst_qualifications',
        'mst_sexual_orientations', 'mst_school_types', 'mst_main_earner_occupations', 'mst_currencies',
    ],
    
    'master_table_names' => 'mst_candidate_job_statuses|mst_employer_types|mst_regions|mst_working_arrangements|mst_customer_types|mst_legal_tech_tools|mst_tech_tools|mst_languages|mst_sexes|mst_genders|mst_channels|mst_cultural_backgrounds|mst_candidate_statuses|mst_faiths|mst_qualifications|mst_sexual_orientations|mst_school_types|mst_currencies|mst_main_earner_occupations|mst_desired_countries',
        
];


/* Pending */
//First Gen HE
//Parents’ HE
//Free School Meals
// Visa 
// Disability : Yes/No/Prefer not to say