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
<div class="projects form large-8 medium-16 columns content float: left">
    <h3><?= __('Add Project') ?></h3>
    <?= $this->Form->create($project) ?>
        <?php
            // jQuery UI datepicker
            echo $this->Form->control('project_name');
            echo $this->Form->control('created_on', ['type' => 'text', 'readonly' => true, 'label' => 'Starting Date', 'id' => 'datepicker']);
            echo $this->Form->control('description');
            echo $this->Form->control('customer');
            echo $this->Form->control('requirements_link');
            // There are no public projects anymore so all the projects work as private projects.
            echo $this->Form->control('is_public', ['type' => 'hidden', 'value' => 0]);
            echo $this->Form->button(__('Submit'));
        ?>
    <?= $this->Form->end() ?>
</div>
<script> 
    $("#datepicker").datepicker({
        dateFormat: 'MM d, yy',        
        minDate: '-6M',
        maxDate: '+6M', 
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date"       
    });
</script>
