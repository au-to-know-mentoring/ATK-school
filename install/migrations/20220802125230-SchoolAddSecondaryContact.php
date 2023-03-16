<?php

class SchoolAddSecondaryContact extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable('school_student_contact_mapping', 'is_secondary_contact', 'boolean', ['default' => 0, 'null' => true]);
        $this->addColumnToTable('school_student_contact_mapping', 'notes', 'string', ['null' => true]);
        $this->addColumnToTable('school_class_data', 'dt_end_date', 'datetime', ['null' => true]);
        $this->addColumnToTable('school_class_data', 'topic', 'string', ['null' => true]);
        $this->addColumnToTable('school_class_data', 'notes', 'string', ['null' => true]);
    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable("school_student_contact_mapping", 'is_secondary_contact');
        $this->removeColumnFromTable("school_student_contact_mapping", 'notes');
        $this->removeColumnFromTable("school_class_data", 'dt_end_date');
        $this->removeColumnFromTable("school_class_data", 'topic');
        $this->removeColumnFromTable("school_class_data", 'notes');
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
