<?php

class SchoolAddSecondaryStudentContacts extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

                if (!$this->hasTable("school_student_contact_mapping")) { //it can be helpful to check that the table name is not used
                    $this->table("school_student_contact_mapping", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    ->addIdColumn('student_id')
                    ->addIdColumn('contact_id')
                    ->addBooleanColumn('is_main_contact')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }

    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_student_contact_mapping') ? $this->dropTable('school_student_contact_mapping') : null;
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
