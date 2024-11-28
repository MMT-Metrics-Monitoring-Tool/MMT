<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>

<div class="members form large-8 medium-16 columns content float: left">
    <?php
        $admin = $this->request->getSession()->read('is_admin');
        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
        $userid = $member->user_id;
        $target_hours = 130;

    if ($member->target_hours != null) {
        $target_hours = $member->target_hours;
    }
        $queryName = Cake\ORM\TableRegistry::get('Users')
            ->find()
            ->select(['first_name','last_name', 'role'])
            ->where(['id =' => $userid])
            ->toArray();
            
    if ($queryName != null) { ?>
        <?= $role = "";
        if ($queryName[0]['role'] == 'inactive') {
            $role = "(inactive)";
        }?>
            <h3><?= __('Edit member: ') . $queryName[0]['first_name'] . " " . $queryName[0]['last_name']
            . " " . $role ?></h3>    
    <?php } ?>
        <?php
            $projid = $this->request->getSession()->read('selected_project')['id'];

            $reports = Cake\ORM\TableRegistry::get('weeklyreports')->find()
                ->select(['id'])
                ->where(['project_id =' => $projid])
                ->toArray();
                            
            // If project has weeklyreports, member cannot be deleted.
        if (!$reports) {
            ?>
                <div id="navbutton">
            <?php
            $this->Form->postLink(
                __('Delete member'),
                ['action' => 'delete', $member->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $member->id)]
            );
        }
        ?>
    </div>
    <?= $this->Form->create($member, ['style' => 'float: left; width: 100%;']) ?>
            <?php

            if ($admin || $supervisor) {
                echo $this->Form->control(
                    'project_role',
                    ['options' => array('developer' => 'developer', 'senior_developer' => 'senior developer', 'supervisor' => 'coach', 'client' => 'customer')]
                );
            }

            echo $this->Form->control('target_hours', array('value' => $target_hours, 'min' => 0, 'style' => 'width: 40%;'));
     
            ?><div style="overflow: auto"><div class="columns medium-6 no-padding"><?php
            
            
if ($admin || $supervisor) {
    // Using jQuery UI datepicker
    // Starting date
    Cake\I18n\Time::setToStringFormat('MMMM d, yyyy');
    echo $this->Form->control('starting_date', ['type' => 'text', 'readonly' => true, 'id' => 'datepicker1']);
            
    // Ending date
    echo $this->Form->control('ending_date', ['type' => 'text', 'readonly' => true, 'id' => 'datepicker2']);
    ?>
            </div>
            <div class="columns medium-6 no-padding reset-buttons">
            
                <input type="button" value="Clear starting date" id="resetStart" /><br>
                <input type="button" value="Clear ending date" id="resetEnd" />

            </div></div>
            <?php
}
?> 
            <?php
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
            echo $this->Form->button(__('Submit'));
            
            $isAdmin = $this->request->getSession()->read('is_admin');
            ?>           
    <?= $this->Form->end() ?>
</div>

<script>         
    // minDate is the date the project was created, for admin there is no min date
    // maxDate is the current day
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
    });

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
            datepicker2.datepicker("option", "minDate", new Date(dateStr));
        }
});

    datepicker2.on('change', function() {
        var minDate = datepicker1.datepicker("getDate");
        var maxDate = datepicker2.datepicker("getDate");
        if(maxDate < minDate) {
            datepicker2.datepicker("setDate", "");
            alert("Ending date must be after starting date");
        }
});
    
    // Resetting datepickers
        var date1 = $("input[id$='datepicker1']");
        var date2 = $("input[id$='datepicker2']");
        $("#resetStart").on('click', function(){
            date1.attr('value','');
                date1.each(function(){
                    $(this).datepicker('setDate', null); 
                }); 
    });
        $("#resetEnd").on('click', function(){
            date2.attr('value','');
                date2.each(function(){
                    $(this).datepicker('setDate', null); 
                });
     
    });
</script>
