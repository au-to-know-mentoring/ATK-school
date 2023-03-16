<?php

class SchoolStudentContactMapping extends DbObject {

    public $student_id;
    public $contact_id;
    public $is_main_contact;
    public $is_secondary_contact;
    public $is_billing_contact;
    public $contact_relationship;
    public $notes;

    public function getContact() {
        return AuthService::getInstance($this->w)->getContact($this->contact_id);
    }

}