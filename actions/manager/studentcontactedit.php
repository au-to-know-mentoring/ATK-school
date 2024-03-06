<?php

use Laminas\Validator\InArray;

function studentcontactedit_GET(Web $w) {
    
    $p = $w->pathMatch("student_id", "student_contact_mapping_id");
    if (!empty($p['student_id'])) {
        // echo $p['student_id'];
        $student = SchoolService::getInstance($w)->GetStudentForId($p['student_id']);
        // echo $student->id;
        $student_contact = $student->getContact();
        $main_contact = $student->getMainContact();
        
    } else {
        $w->error("No Student Id provided for Contact", "/school");
    }

    

    // echo $p['student_contact_mapping_id'];

    if (!empty($p['student_contact_mapping_id'])) {
        // echo "test";
        $student_contact_mapping = SchoolService::getInstance($w)->GetStudentContactMappingForId($p['student_contact_mapping_id']);
        // echo $student_contact_mapping->id;
        $contact = $student_contact_mapping->getContact();
        $w->ctx("title","Edit Contact for " . $student_contact->getFullName() );
    } else {
        $student_contact_mapping = new SchoolStudentContactMapping($w);
        $contact = new Contact($w);
        $w->ctx("title","Add Contact for " . $student_contact->getFullName());
    }


    $form = [
        "Contact Details" => [
            [
                ["First Name", "text", "contact_firstname", $contact->firstname],
                ["Last Name", "text", "contact_lastname", $contact->lastname]
            ],
            [
                ["Home Phone", "text", "contact_homephone", $contact->homephone],
                ["Mobile", "text", "contact_mobile", $contact->mobile],
                ["Email", "text", "contact_email", $contact->email]
            ]
        ],
        "Other details" => [
            [
                ['Contact Relationship to Participant', 'text', 'contact_relationship', $student_contact_mapping->contact_relationship],
                ['Main Contact', 'checkbox', 'is_main_contact', $student_contact_mapping->is_main_contact],
                ['Secondary Contact', 'checkbox', 'is_secondary_contact', $student_contact_mapping->is_secondary_contact],
                ['Billing Contact', 'checkbox', 'is_billing_contact', $student_contact_mapping->is_billing_contact],
            ],
            [
                ['Notes', 'textarea', 'notes', $student_contact_mapping->notes]
            ]
        ]
    ];

    $post_url = '/school-manager/studentcontactedit/' . $p['student_id'];
    if (!empty($p['student_contact_mapping_id'])) {
        $post_url .= "/" . $p['student_contact_mapping_id'];
    }

    $w->out(Html::multiColForm($form, $post_url));

}

function studentcontactedit_POST(Web $w) {
    $p = $w->pathMatch("student_id", "student_contact_mapping_id");
    if (!empty($p['student_id'])) {
        $student = SchoolService::getInstance($w)->GetStudentForId($p['student_id']);
        $student_contact = $student->getContact();
        $main_contact = $student->getMainContact();
        $billing_contact = $student->getBillingContact();
    } else {
        $w->error("No Student Id provided for Contact", "/school");
    }

    if (!empty($p['student_contact_mapping_id'])) {
        $student_contact_mapping = SchoolService::getInstance($w)->GetStudentContactMappingForId($p['student_contact_mapping_id']);
        $contact = $student_contact_mapping->getContact();
        $w->ctx("title","Edit Contact");
    } else {
        $student_contact_mapping = new SchoolStudentContactMapping($w);
        $contact = new Contact($w);
        $w->ctx("title","Add Contact for " . $student_contact->getFullName());
    }

    

    //fill the main contact details
    $contact->firstname = $_POST['contact_firstname'];
    $contact->lastname = $_POST['contact_lastname'];
    $contact->homephone = $_POST['contact_homephone'];
    $contact->mobile = $_POST['contact_mobile'];
    $contact->email = $_POST['contact_email'];
    $contact->insertOrUpdate();

    //store the student contact mapping 
    
    $student_contact_mapping->student_id = $student->id;
    $student_contact_mapping->contact_id = $contact->id;
    $student_contact_mapping->contact_relationship = $_POST['contact_relationship'];
    $student_contact_mapping->notes = $_POST['notes'];

    //check if ok to set or need to remove previous main and billing contact
    if (!empty($_POST['is_main_contact']) && !empty($_POST['is_billing_contact']) && !empty($main_contact) && !empty($billing_contact) && $main_contact->id == $billing_contact->id) { // change made at the styart of the if statement adding in the !empty()
       
        $old_main_contact_mapping = $student->getMainContactMapping();
        $old_main_contact_mapping->is_main_contact = false;
        $old_main_contact_mapping->is_billing_contact = false;
        $old_main_contact_mapping->insertOrUpdate();
        
        $student_contact_mapping->is_main_contact = $_POST['is_main_contact'] ? true : false;
        $student_contact_mapping->is_billing_contact = $_POST['is_billing_contact'] ? true : false;

    } else {
    
        if (!empty($_POST['is_main_contact'])) {// !empty change ad
            // check and remove old main contact
            if (!empty($main_contact) && $main_contact->id != $contact->id) {
                $old_main_contact_mapping = $student->getMainContactMapping();
                $old_main_contact_mapping->is_main_contact = false;
                $old_main_contact_mapping->insertOrUpdate();
            }
            
        }
        $student_contact_mapping->is_main_contact = !empty($_POST['is_main_contact']) ? true : false;
        
        if (!empty($_POST['is_billing_contact'])) {
            // check and remove old billing contact
            if (!empty($billing_contact) && $billing_contact->id != $contact->id) {
                $old_billing_contact_mapping = $student->getBillingContactMapping();
                $old_billing_contact_mapping->is_billing_contact = false;
                $old_billing_contact_mapping->insertOrUpdate();
            }
            
        }
        $student_contact_mapping->is_billing_contact = !empty($_POST['is_billing_contact']) ? true : false;
    }
     if (!empty($_POST['is_secondary_contact'])) {
        if (empty($_POST['is_main_contact'])) {
            // check for existing secondary cointact
            $old_secondary_contact = $student->getSecondaryContactMapping();
            if (!empty($old_secondary_contact)) {
                $old_secondary_contact->is_secondary_contact = false;
                $old_secondary_contact->insertOrUpdate();
            }
            $student_contact_mapping->is_secondary_contact = true;
        } else {
            $student_contact_mapping->is_secondary_contact = false;
        }

    }else {$student_contact_mapping->is_secondary_contact = false;}

   
    
   
    
    


    

    $student_contact_mapping->insertOrUpdate();
    

    if (!empty($p['student_contact_mapping_id'])) {
        $msg = "Contact Details Updated";
    } else {
        $msg = "New Contact Saved";
    }

    $w->msg($msg, "/school-manager/studentview/" . $p['student_id']);

}