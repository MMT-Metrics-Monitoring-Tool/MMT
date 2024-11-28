<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>


<div class="workinghours form large-8 medium-16 columns content float: left">
            <?php
            
            // Print out the name of the member whose workinghour will be edited
            // for admins, supervisors and senior developers
            $admin = $this->request->getSession()->read('is_admin');
            $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            $senior_developer = ( $this->request->getSession()->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
            
            if ($admin || $supervisor || $senior_developer) {
                $userid = $workinghour->member->user_id;
            
                $queryName = Cake\ORM\TableRegistry::get('Users')
                ->find()
                ->select(['first_name','last_name'])
                ->where(['id =' => $userid])
                ->toArray();
            
                if ($queryName != null) { ?>
                    <h3><?= __('Edit logged time for ') . $queryName[0]['first_name'] . " " . $queryName[0]['last_name'] ?></h3>    
                <?php }
            } else { ?>
                <h3><?= __('Edit logged time') ?></h3>
            <?php } ?>
            <div id="navbutton">
                <?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $workinghour->id],
                    ['confirm' => __('Are you sure you want to delete # {0}?', $workinghour->id)]
                )
?>
            </div>
            <?= $this->Form->create($workinghour) ?>
        <?php
            // change the format of the date that comes from the db
            Cake\I18n\Time::setToStringFormat('MMMM d, yyyy');
            
            echo $this->Form->control('date', ['type' => 'text', 'readonly' => true]);
        ?> </br>
        <?php
            echo $this->Form->control('description');
            echo $this->Form->control('duration', array('min' => 0, 'style' => 'width: 40%;'));
            echo $this->Form->control('worktype_id', ['options' => $worktypes]);
        /*
         *
         */
            /*
             * Req 21:
             * The weeks when the weekly reports were sent or if there are no reports,
             * the date when the project was created is fetched from the db.
             */
           
            $project_id = $this->request->getSession()->read('selected_project')['id'];
            /*
            $query = Cake\ORM\TableRegistry::get('Weeklyreports')
                ->find()
            ->select(['year','week'])
                ->where(['project_id =' => $project_id])
                ->toArray();
            if ($query != null) {
                // picking out the week of the last weekly report from the results
                $max = max($query);
                $maxYear = $max['year'];
                $maxWeek = $max['week'];

                // $mDate is the first day of the new weeklyreport week (monday)
                $monday = new DateTime();
                $monday->setISODate($maxYear,$maxWeek,8);
                $mDate1 = $monday->format('d M Y');
                $mDate = date('d M Y', strtotime($mDate1));
            } */
            // There are no weekly reports.
            // else {
                $project_id = $this->request->getSession()->read('selected_project')['id'];
                $query2 = Cake\ORM\TableRegistry::get('Projects')
                    ->find()
                    ->select(['created_on'])
                    ->where(['id =' => $project_id])
                    ->toArray();
                
        foreach ($query2 as $result) {
            $temp = date_parse($result);
            $year = $temp['year'];
            $month = $temp['month'];
            $day = $temp['day'];
                    
            // $mDate is the date project was created on
            $mDate = date("d M Y", mktime(0, 0, 0, $month, $day, $year));
        }
            // }
            echo $this->Form->button(__('Submit'));
        ?>    
 
    <?= $this->Form->end() ?>
</div>

<script> 
    /*
     * Req 21
     * minDate is the date project was created
     * maxDate is the current day
     */
    $( "#date" ).datepicker({
        dateFormat: "MM d, yy",
        minDate: new Date('<?php echo $mDate; ?>'),
        maxDate: '0', 
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date"       
    });
  </script>