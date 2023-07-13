<?php 
$w->enqueueStyle(array("name" => "main.css", "uri" => "/modules/school/includes/fullcalendar-5.10.0/lib/main.css", "weight" => 1010));
$w->enqueueScript(array("name" => "main.js", "uri" => "/modules/school/includes/fullcalendar-5.10.0/lib/main.js", "weight" => 1010));


?>
<style>
  /* .Available {
    background-color: green;
  }
  .Unavailable {
    background-color: red;
  }
  .Late {
    background-color: orange;
  }
  .Canceled {
    background-color: grey;
  } */

  <?php 
  
  foreach($calendar_settings as $mentor_calendar_settings){
    echo ".teacher_" . $mentor_calendar_settings->teacher_id . " { " . "background-color: " . $mentor_calendar_settings->colour . "; } ";
  }


  ?>




</style>


 <script>

document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
    headerToolbar: {
        start: "today prev,next",
        center: 'title',
        end: 'dayGridMonth,timeGridWeek,timeGridDay,list'
    },
    events: '/school-manager/myfeed<?php echo (!empty($teacher_id)) ? '/' . $teacher_id : ''; ?>',
    //events: JSON.parse('<?php //echo $events; ?>'),
    contentHeight: 650,
  });
  calendar.render();
});

</script>

<?php

  // if ($user->hasRole('school_manager')) {
  //   echo Html::b("/school_teacher/teachercalendar/?")
  // }
  if ($user->hasRole('school_teacher') && !$user->hasRole('school_manager')) {
    echo Html::b("/school-teacher/editavailability/teacher/" . $teacher_id, "Add New Availability");
  }
?>
<?php 
    echo Html::filter("Filters", $filter_data, "/school-manager/calendar", "GET");
    if (!empty($teacher_id)) {
        echo Html::b('/school-teacher/editavailability/teacher/' . $teacher_id, 'Add New Availability for ' . SchoolService::getInstance($w)->GetTeacherForId($teacher_id)->getFullName());
    }
    echo Html::box("/school-manager/editCalendarSettings", "Calendar Settings", true);
    echo Html::box("/school-manager/editCustomCalendar", "Add Custom Calendar", true);
    echo Html::box("/school-manager/editCalendarEvent", "Add Calendar Event", true);
?>

<div id='calendar'></div>