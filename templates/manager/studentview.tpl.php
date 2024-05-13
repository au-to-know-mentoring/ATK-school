<?php 
    if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
        if (!$has_billing_contact) {
            echo Html::b("/school-manager/studentcontactedit/" . $student_id, "No billing contact set. Please add billing contact", null, null, false, "warning");
            echo "</br></br>";
            //"<p class='warning'>No billing contact set. Please add billing contact</p></br> ";
        }
        echo Html::b("/school-manager/studentedit/" . $student_id, "Edit");
        echo Html::b("/school-manager/studentcontactedit/" . $student_id, "Add New Contact");
        echo Html::b("/school-manager/classdataedit/" . $student_id, "Add New Class");
    }
?>

<?php echo $detailsTable; ?>
<?php echo $classes_table; ?>