<?php

class SchoolInitialMigration extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);


        if (!$this->hasTable("school_teacher")) { //it can be helpful to check that the table name is not used
            $this->table("school_teacher", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addIdColumn('contact_id')
            ->addIntegerColumn('max_students')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();
        }

        if (!$this->hasTable("school_student")) { //it can be helpful to check that the table name is not used
            $this->table("school_student", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addIdColumn('contact_id')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();
        }

        if (!$this->hasTable("school_class_data")) { //it can be helpful to check that the table name is not used
            $this->table("school_class_data", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addIdColumn('student_id')
            ->addIdColumn('teacher_id')
            ->addDateTimeColumn('dt_class_date')
            ->addBooleanColumn('is_recurring')
            ->addStringColumn('frequency')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();
        }

        if (!$this->hasTable("school_class_instance")) { //it can be helpful to check that the table name is not used
            $this->table("school_class_instance", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addIdColumn('class_data_id')
            ->addIdColumn('substitute_teacher_id')
            ->addDateTimeColumn('dt_class_date')
            ->addStringColumn('status')
            ->addStringColumn('teachers_notes')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();
        }



    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_teacher') ? $this->dropTable('school_teacher') : null;
        $this->hasTable('school_student') ? $this->dropTable('school_student') : null;
        $this->hasTable('school_class_data') ? $this->dropTable('school_class_data') : null;
        $this->hasTable('school_class_instance') ? $this->dropTable('school_class_instance') : null;
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
