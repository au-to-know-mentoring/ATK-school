<?php

class SchoolCalendarSettings extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
            ->setType('biginteger')
            ->setIdentity(true);

            
        if (!$this->hasTable("school_calendar_settings")) { 
            $this->table("school_calendar_settings", [
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addIdColumn('user_id')
                ->addIdColumn('teacher_id')
                ->addBooleanColumn('is_view_class')
                ->addBooleanColumn('is_view_availability')
                ->addStringColumn('colour')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }
    }

    public function down()
    {
        $this->hasTable('school_calendar_settings') ? $this->dropTable('school_calendar_settings') : null;
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
