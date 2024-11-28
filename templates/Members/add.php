<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>

<!-- 
    If the lower navigation bar needed, links go here
    <ul class="side-nav">
    </ul>
-->

<div class="members form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($member) ?>
    <fieldset>
        <legend><?= __('Add Member') ?></legend>
        <?php
            echo $this->Form->control('user_id', ['type' => 'hidden']);
        ?><div class="ui-widget"><?php
            echo $this->Form->control('email', ['options' => $users, 'type' => 'text',
                'required' => true, 'label' => 'Email of the user']);
            ?></div><?php
if ($this->request->getSession()->read('selected_project_role') == 'senior_developer') {
    echo $this->Form->control(
        'project_role',
        ['options' => array('client' => 'customer', 'developer' => 'developer', 'senior_developer' => 'senior developer'), 'empty' => ' ']
    );
} else {
    echo $this->Form->control(
        'project_role',
        ['options' => array('client' => 'customer', 'developer' => 'developer', 'senior_developer' => 'senior developer', 'supervisor' => 'coach'), 'empty' => ' ']
    );
}
                        
            echo $this->Form->control('target_hours', array('type' => 'integer', 'value' => '130', 'min' => 0, 'style' => 'width: 15%;'));

            // jQuery UI datepicker
            echo $this->Form->control('starting_date', ['type' => 'text', 'readonly' => true, 'id' => 'datepicker1', 'value' => '']);
?> </br>
            <?php
            echo $this->Form->control('ending_date', ['type' => 'text', 'label' => 'Ending date (preferably leave this field empty)', 'readonly' => true, 'id' => 'datepicker2', 'value' => '']);

            // Fetching from the db the date when the project was created
            $project_id = $this->request->getSession()->read('selected_project')['id'];
            $query = Cake\ORM\TableRegistry::get('Projects')
                ->find()
                ->select(['created_on'])
                ->where(['id =' => $project_id])
                ->toArray();
                
            foreach ($query as $result) {
                $temp = date_parse($result);
                $year = $temp['year'];
                $month = $temp['month'];
                $day = $temp['day'];
                $mDate = date("d M Y", mktime(0, 0, 0, $month, $day, $year));
            }

            ?>
            <input type="button" value="Clear ending date" id="resetEnd" /><br>
            <?php

            echo $this->Form->button(__('Submit'));
                        
            $isAdmin = $this->request->getSession()->read('is_admin');
            ?>    
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<script> 
    var datepicker1 = $("#datepicker1");
    var datepicker2 = $("#datepicker2");
    
    datepicker2.datepicker({
        dateFormat: "MM d, yy",
        minDate: <?php if ($isAdmin) {
            ?> null<?php
                 } else {
                        ?> new Date('<?php echo $mDate; ?>') <?php
                 } ?>,
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date"
        }       
    );

    datepicker1.datepicker({
        dateFormat: "MM d, yy",
        minDate: <?php if ($isAdmin) {
            ?> null<?php
                 } else {
                        ?> new Date('<?php echo $mDate; ?>') <?php
                 } ?>,
        maxDate: '0', 
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        onSelect: function(dateStr) {
            var minDate = new Date(dateStr);
            var maxDate = datepicker2.datepicker("getDate");

        if(maxDate != null && minDate.getTime() > maxDate.getTime()) {
            datepicker2.datepicker("setDate", "");
            alert("Ending date must be after starting date");
        }

    datepicker2.datepicker("option", "minDate", minDate);
}
    }     
);
    //Alert the user if ending date is invalid
    datepicker2.on('change', function() {
    var minDate = datepicker1.datepicker("getDate");
    var maxDate = datepicker2.datepicker("getDate");
    if(maxDate < minDate) {
        datepicker2.datepicker("setDate", "");
        alert("Ending date must be after starting date");
    }
});
    
        // Resetting ending date
        var date = $("input[id$='datepicker2']");
        $("#resetEnd").on('click', function(){
            date.attr('value','');
                date.each(function(){
                    $(this).datepicker('setDate', null); 
                });
    });

</script>
<script>

    var emails = [ 
           <?php
            if ($users != null) {
                foreach ($users as $user) {
                       echo "\"" . $user . "\",";
                }
            }?>
    ];

    $( "#autocomplete" ).autocomplete({
      source: function( request, response ) {
              var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
              response(($.grep( emails, function( item ){
                  return matcher.test( item );
              }) ).slice(0, 10));
          }
    });
</script>
