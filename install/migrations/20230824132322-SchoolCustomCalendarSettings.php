<?php

class SchoolCustomCalendarSettings extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

        if (!$this->hasTable("school_custom_calendar_settings")) {
            $this->table("school_custom_calendar_settings", [
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addIdColumn('user_id')
                ->addIdColumn('custom_calendar_id')
                ->addBooleanColumn('is_view_calendar')
                ->addStringColumn('colour')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }

        if (!$this->hasTable("school_custom_calendar_event_details")) {
            $this->table("school_custom_calendar_event_details", [
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addIdColumn('custom_calendar_id')
                ->addStringColumn('event_name')
                ->addStringColumn('event_description')
                ->addStringColumn('timezone')
                ->addStringColumn('status')
                ->addBooleanColumn('is_repeat')
                ->addStringColumn('repeat_interval')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }

        if (!$this->hasTable("school_custom_calendar_event_instance")) {
            $this->table("school_custom_calendar_event_instance", [
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addIdColumn('event_details_id')
                ->addIdColumn('custom_calendar_id')
                ->addStringColumn('event_name')
                ->addStringColumn('event_description')
                ->addStringColumn('timezone')
                ->addStringColumn('status')
                ->addDateTimeColumn('dt_start_time')
                ->addDateTimeColumn('dt_end_time')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }

    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_custom_calendar_settings') ? $this->dropTable('school_custom_calendar_settings') : null;
        $this->hasTable('school_custom_calendar_event_details') ? $this->dropTable('school_custom_calendar_event_details') : null;
        $this->hasTable('school_custom_calendar_event_instance') ? $this->dropTable('school_custom_calendar_event_instance') : null;
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
