<?php 
    if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
        echo Html::b("/school-manager/studentedit/" . $student_id, "Edit");
    }
?>

<?php echo $detailsTable; ?>
<?php echo $classes_table; ?>