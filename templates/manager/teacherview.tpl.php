<?php 
    echo Html::b('/school-manager/teacheredit/' . $teacher_id, 'Edit Mentor Details'); 
    echo Html::b('/school-manager/calendar?calendar__teacher-id=' . $teacher_id, 'View Mentors Calendar');    
?>
<?php echo $detailsTable; ?>
<?php echo $studentTable; ?>