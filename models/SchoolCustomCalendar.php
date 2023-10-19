<?php
//SchoolN
class SchoolCustomCalendar extends DbObject
{
    public $calendar_name;
    public $user_id;

    public function getAllEventsForCalendarIdAndDateRange($calendar_id, $dateArray)
    {
        return $this->getObjects('SchoolCustomCalendarEvent', [
            'is_deleted' => 0,
            'custom_calendar_id' => $calendar_id,
            // 'dt_start_time <= ?' => date('Y-m-d 00:00:00', strtotime($dateArray['end']))
        ]);
    }
}
