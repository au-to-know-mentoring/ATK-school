<?php

class SchoolAddTeacherAvailability extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

                if (!$this->hasTable("school_teacher_availability")) { //it can be helpful to check that the table name is not used
                    $this->table("school_teacher_availability", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    ->addIdColumn('teacher_id')
                    ->addDateTimeColumn('dt_start_time')
                    ->addDateTimeColumn('dt_end_time')
                    ->addStringColumn('type')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }
    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_teacher_availability') ? $this->dropTable('school_teacher_availability') : null;
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
