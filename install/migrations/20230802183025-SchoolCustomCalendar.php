<?php

class SchoolCustomCalendar extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
            ->setType('biginteger')
            ->setIdentity(true);

        if (!$this->hasTable("school_custom_calendar")) {
            $this->table("school_custom_calendar", [
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addIdColumn('user_id')
                ->addStringColumn('calendar_name')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }
    }


    public function down()
    {
        $this->hasTable('school_custom_calendar') ? $this->dropTable('school_custom_calendar') : null;
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
