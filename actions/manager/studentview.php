<?php

function studentview_ALL(Web $w) {
    $p = $w->pathMatch('student_id','class_data_id');

    $loggedInUser = AuthService::getInstance($w)->user();

    

    if (empty($p['student_id'])) {
        $w->error('No Student Id found', '/school-teacher/studentlist');
    }

    $student = SchoolService::getInstance($w)->GetStudentForId($p['student_id']);
    
    if (empty($student)) {
        $w->error('No Student found for id', '/school-teacher/studentlist');
    }
    
    $w->ctx('student_id', $student->id);


    $student_name = $student->getContact()->getFullName();

    $w->ctx('title', $student_name ? $student_name : 'No Name Provided');

    

    $student_contact = $student->getContact();
    
    $main_contact_mapping = $student->getMainContactMapping();
    $main_is_billing = false;

    if (!empty($main_contact_mapping) && $main_contact_mapping->is_billing_contact) {
        $main_is_billing = true;
    }

    $main_contact = null;
    if (!empty($main_contact_mapping)) {
        $main_contact = $main_contact_mapping->getContact();
    }
    $secondary_contact_mapping = $student->getSecondaryContactMapping();
    $Secondary_is_billing = false;

    if (!empty($secondary_contact_mapping) && $secondary_contact_mapping->is_billing_contact) {
        $Secondary_is_billing = true;
    }

    //smaple time for student
    $time = null;
    if (!empty($student->timezone)) {
        $time = new DateTime("now", new DateTimeZone($student->timezone));
    }
    


    $studentData = [
        "Participent Details" => [
            [
                ["Home Phone", "text", "student_homephone", $student_contact->homephone],
                ["Mobile", "text", "student_mobile", $student_contact->mobile],
                ["Email", "text", "student_email", $student_contact->email]
            ],
            [
                ["State", "text", "state", $student->state],
                ["Timezone", "text", "timezone", $time ? $student->timezone . ' - ' . $time->format('H:i') : '']
            ]
        ],
        // "Account Details" => [
        //     [
        //         ["Rate", "text", "rate", $student->rate]
        //     ]
        // ]
    ];

    

    if ($main_is_billing) {
        $main_contact_section_title = "Main and Billing Contact Details";
    } else {
        $main_contact_section_title = "Main Contact Details";
    }

    if (!empty($main_contact)){
        $studentData[$main_contact_section_title] = [
            [
                ["Name", "text", "main_contact_name", $main_contact->getFullName()],
                // ["Last Name", "text", "main_contact_lastname", $main_contact->lastname],
                ["Relationship", "text", "main_contact_relationship", $main_contact_mapping->contact_relationship]
            ],
            [
                ["Home Phone", "text", "main_contact_homephone", $main_contact->homephone],
                ["Mobile", "text", "main_contact_mobile", $main_contact->mobile],
                ["Email", "text", "main_contact_email", $main_contact->email],
                ['Notes', 'text', 'notes', $main_contact_mapping->notes]
            ],
            [
                ["Actions", "text", "editButton", Html::b('/school-manager/studentcontactedit/' . $student->id . '/' . $main_contact_mapping->id, 'Edit')]
            ]
        ];
    }

    $secondary_contact_mapping = $student->getSecondaryContactMapping();
    //$billing_contact_mapping = $student->getBillingContactMapping();
    if (!empty($secondary_contact_mapping) && $secondary_contact_mapping->is_billing_contact) {
        $secondary_contact_section_title = "Secondary and Billing Contact Details";
    } else {
        $secondary_contact_section_title = "Secondary Contact Details";
    }

    if (!empty($secondary_contact_mapping)) {
        $secondary_contact = $secondary_contact_mapping->getContact();
        $studentData[$secondary_contact_section_title] = [
            [
                ["Name", "text", "main_contact_name", $secondary_contact->getFullName()],
                // ["Last Name", "text", "main_contact_lastname", $main_contact->lastname],
                ["Relationship", "text", "main_contact_relationship", $secondary_contact_mapping->contact_relationship]
            ],
            [
                ["Home Phone", "text", "main_contact_homephone", $secondary_contact->homephone],
                ["Mobile", "text", "main_contact_mobile", $secondary_contact->mobile],
                ["Email", "text", "main_contact_email", $secondary_contact->email],
                ['Notes', 'text', 'notes', $secondary_contact_mapping->notes]
            ],
            [
                ["Actions", "text", "editButton", Html::b('/school-manager/studentcontactedit/' . $student->id . '/' . $secondary_contact_mapping->id, 'Edit')]
            ]
        ];
    }
    
    
    //check for billing contact and add to array
    if (!$main_is_billing && !$Secondary_is_billing) {
        $billing_contact_mapping = $student->getBillingContactMapping();
        if (!empty($billing_contact_mapping)) {
            $billing_contact = $billing_contact_mapping->getContact();
            $studentData["Billing Contact Details"] = [
                [
                    ["Name", "text", "billing_contact_name", $billing_contact->getFullName()],
                    // ["Last Name", "text", "main_contact_lastname", $main_contact->lastname],
                    ["Relationship", "text", "billing_contact_relationship", $billing_contact_mapping->contact_relationship]
                ],
                [
                    ["Home Phone", "text", "billing_contact_homephone", $billing_contact->homephone],
                    ["Mobile", "text", "billling_contact_mobile", $billing_contact->mobile],
                    ["Email", "text", "billing_contact_email", $billing_contact->email],
                    ['Notes', 'text', 'notes', $billing_contact_mapping->notes]
                ],
                [
                    ["Actions", "text", "editButton", Html::b('/school-manager/studentcontactedit/' . $student->id . '/' . $billing_contact_mapping->id, 'Edit')]
                ]
            ];
        }
    }

    

    //check for more contacts and add them to the array
    $other_contact_mappings = $student->getContactMappings();
    if (!empty($other_contact_mappings)) {
        foreach($other_contact_mappings as $contact_mapping) {
            $contact = $contact_mapping->getContact();
            $studentData["Contact Details: " . $contact->getFullName()] = [
                [
                    ["Name", "text", "main_contact_name", $contact->getFullName()],
                    // ["Last Name", "text", "main_contact_lastname", $main_contact->lastname],
                    ["Relationship", "text", "main_contact_relationship", $contact_mapping->contact_relationship]
                ],
                [
                    ["Home Phone", "text", "main_contact_homephone", $contact->homephone],
                    ["Mobile", "text", "main_contact_mobile", $contact->mobile],
                    ["Email", "text", "main_contact_email", $contact->email],
                    ['Notes', 'text', 'notes', $contact_mapping->notes]
                ],
                [
                    ["Actions", "text", "editButton", Html::b('/school-manager/studentcontactedit/' . $student->id . '/' . $contact_mapping->id, 'Edit')]
                ]
            ];   
        }
       
    }


    $w->ctx('detailsTable', Html::multiColTable($studentData));

    $classes_table = [];
    $classes = SchoolService::getInstance($w)->GetClassDataForStudentId($student->id);
    if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
        $classes_table_headers = ['teacher','next date','time','frequency', 'Status', 'Rate', 'actions'];
    } else {
        $classes_table_headers = ['teacher','next data', 'time', 'link'];
    }
    
    if (!empty($classes)) {
        foreach ($classes as $class) {
            $row = [];
            $row[] = $class->getTeacher()->getContact()->getFullName();
            $row[] = $class->getNextDate();
            $row[] = date('H:i', $class->dt_class_date);
            if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
                $row[] = $class->frequency;
                $row[] = $class->status;
                $row[] = $class->rate;
                $actions = [];
            
                $actions[] = Html::b('/school-manager/classdataedit/' . $student->id . '/' . $class->id, 'Edit');
                //$actions[] = Html::b('/school-teacher/viewclassdata/' . $class->id, 'View');
                $row[] = implode($actions);
            } else {
                $row[] = $class->link;
            }
            
            
            $classes_table[] = $row;
        }
    }

    $w->ctx('classes_table', Html::table($classes_table, null, "tablesorter", $classes_table_headers));

}