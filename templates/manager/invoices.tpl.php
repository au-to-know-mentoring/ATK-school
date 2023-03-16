<div class="tabs">
    <div class="tab-head">
        <a href="#uninvoiced">Uninvoiced Sessions</a>
        <a href="#newinvoiced">New Invoices</a>
        <a href="#unpaid">Unpaid Invoices</a>
    </div>
    <div class="tab-body">
        <div id="uninvoiced" class="clearfix">
            <?php echo Html::box('/school-manager/generateinvoices', 'Genreate Invoices', true); ?>
            <?php echo $uninvoiced_table; ?>
        </div>
        <div id ="newinvoiced" class="clearfix">
        <?php echo $new_table; ?>
        </div>
        <div id ="unpaid" class="clearfix">
        <?php echo $unpaid_table; ?>
        </div>
    </div>
</div>

