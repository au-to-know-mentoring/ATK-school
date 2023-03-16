<?php

class SchoolAddContactParameters extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable('school_student_contact_mapping', 'is_billing_contact', 'boolean', ['default' => 0, 'null' => true]);
        $this->addColumnToTable('school_student_contact_mapping', 'contact_relationship', 'string', ['null' => true]);

    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable("school_student_contact_mapping", 'is_billing_contact');
        $this->removeColumnFromTable("school_student_contact_mapping", 'contact_relationship');
    }

    public function preText()
    {
        return null;
    }

    public function postText()
    {
        return null;
    }

    public function description()
    {
        return null;
    }
}
