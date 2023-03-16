<?php

class SchoolTeacher extends DbObject {

    public $user_id;
    public $max_students;
    public $state;
    public $timezone;

    public function getUser() {
        return AuthService::getInstance($this->w)->getUser($this->user_id);
    }

    public function getContact() {
        return $this->getUser()->getContact();
    }

    public function getFullName() {
        return $this->getContact()->getFullName();
    }

    public function getClassesCount() {
        $where = [
            "teacher_id"=>$this->id,
            "is_recurring"=>true,
            "is_deleted"=>false
        ];
        $classes = SchoolService::getInstance($this->w)->getObjects("SchoolClassData",$where);
        return empty($classes) ? '0' : Count($classes);
    }

    public function getSelectOptionTitle() {
        return $this->getFullName();
    }

}