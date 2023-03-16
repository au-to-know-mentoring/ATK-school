<?php 
    if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
        echo Html::b("/school-manager/studentedit/" . $student_id, "Edit");
        echo Html::b("/school-manager/studentcontactedit/" . $student_id, "Add New Contact");
        echo Html::b("/school-manager/classdataedit/" . $student_id, "Add New Class");
    }
?>

<?php echo $detailsTable; ?>
<?php echo $classes_table; ?>