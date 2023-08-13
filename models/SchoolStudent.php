<?php

class SchoolStudent extends DbObject {

    public $contact_id;
    public $state;
    public $timezone;
    //public $rate;

    public function getContact() {
        return AuthService::getInstance($this->w)->getContact($this->contact_id);
    }

    public function getFullName() {
        return $this->getContact()->getFullName();
    }

    public function getMainContactMapping() {
        return SchoolService::getInstance($this->w)->GetObject("SchoolStudentContactMapping",['is_deleted'=>0,'student_id'=>$this->id,'is_main_contact'=>1]);
    }

    public function getMainContact() {
        $main_contact = $this->getMainContactMapping();
        if (!empty($main_contact)) {
            return $main_contact->getContact();
        }
        return null;
    }

    public function getBillingContactMapping() {
        return SchoolService::getInstance($this->w)->GetObject("SchoolStudentContactMapping", ['is_deleted'=>0, 'student_id'=>$this->id, 'is_billing_contact'=>1]);
    }

    public function getBillingContact() {
        $billing_contact = $this->getBillingContactMapping();
        if (!empty($billing_contact)) {
            return $billing_contact->getContact();
        }
        return null;
    }

    public function getSecondaryContactMapping(){
        return SchoolService::getInstance($this->w)->GetObject("SchoolStudentContactMapping", ['is_deleted'=>0, 'student_id'=>$this->id, 'is_secondary_contact'=>1]);
    }

    public function getSecondaryContact() {
        $secondaryContact = $this->getSecondaryContactMapping();
        if (!empty($secondaryContact)) {
            return $secondaryContact->getContact();
        }
        return null;
    }

    public function getContactMappings() {
        return SchoolService::getInstance($this->w)->GetObjects("SchoolStudentContactMapping",['is_deleted'=>0,'student_id'=>$this->id,'is_main_contact'=>0, 'is_billing_contact'=>0, 'is_secondary_contact'=>0]);
    }

    public function getContacts() {
        $contacts = [];
        $mappings = $this->getContactMappings();
        if (!empty($mappings)) {
            foreach ($mappings as $mapping) {
                $contacts[] = $mapping->getContact();
            }
        }
        return $contacts;
    }

    public function getStatus($teacher_id = null) {
        if (empty($teacher_id)) {
            // if a student does not have any classes thay are 'new'
            $classData = SchoolService::getInstance($this->w)->GetClassDataForStudentId($this->id);
            if (empty($classData)) {
                return 'New';
            }
            return 'Active';
        }

        //$classData = SchoolService::getInstance($this->w)->

        //return $classData->status;
    }

}