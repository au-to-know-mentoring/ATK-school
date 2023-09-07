<?php

class SchoolCustomCalendarEvent extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
            ->setType('biginteger')
            ->setIdentity(true);


        if (!$this->hasTable("school_custom_calendar_event")) {
            $this->table("school_custom_calendar_event", [
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addIdColumn('custom_calendar_id')
                ->addStringColumn('event_name')
                ->addStringColumn('event_description')
                ->addStringColumn('timezone')
                ->addDateTimeColumn('dt_start_time')
                ->addDateTimeColumn('dt_end_time')
                ->addStringColumn('status')
                ->addBooleanColumn('is_repeat')
                ->addStringColumn('repeat_interval')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }
    }


    public function down()
    {
        $this->hasTable('school_custom_calendar_event') ? $this->dropTable('school_custom_calendar_event') : null;
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
