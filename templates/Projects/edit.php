<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>


<div class="projects form large-8 medium-16 columns content float: left">
        <h3><?= __('Edit Project') ?></h3>
        <?php
        // Delete button not visible to devs or senior developers
        $admin = $this->request->getSession()->read('is_admin');
        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
        if ($admin || $supervisor) { ?>
            <div id="navbutton">
            <?= $this->Form->postLink(
                __('Delete Project'),
                ['action' => 'delete', $project->id],
                ['confirm' => __('Are you sure you want to delete # {0}? Note: you must delete logged hours, members and weekly reports first.', $project->id)]
            )
            ?>
            </div>
        <?php } ?>
        <?= $this->Form->create($project) ?>
        <?php
            echo $this->Form->control('project_name');

            // Req 37: using jQuery UI datepicker
            echo $this->Form->control('finished_date', ['type' => 'text', 'readonly' => true, 'label' => 'Estimated Completion Date', 'id' => 'datepicker']);
        ?> </br>
            <?php

            echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'This is used to make some of the charts. Senior developer should set this. It can be changed later.']);

            echo $this->Form->control('description');
            echo $this->Form->control('customer');
            echo $this->Form->control('requirements_link');

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
    <?= $this->Form->end(); ?>
</div>

<style>
   .input{display:inline;} /* Helps to get the infoicon tooltip stay after estimated completion date input field. */
</style>
<script> 
    /*
     * Req 37:
     * minDate is the date the project was created, no min date if it is admin
     */
    
    $( "#datepicker" ).datepicker({
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
</script>