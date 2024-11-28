
<!-- 
    If the lower navigation bar needed, links go here
    
    <ul class="side-nav">
        
    </ul>
-->

<div class="weeklyreports form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($weeklyreport) ?>
    <fieldset>
        <legend><?= __('Add Weeklyreport, Page 1/4') ?></legend>
        <?php
            $current_weeklyreport = $this->request->getSession()->read('current_weeklyreport');

            use Cake\I18n\Time;

            $now = Time::now();
            $nowWeek = date('W');
            $reportWeek = $now->weekOfYear -1;
            
        if (!is_null($current_weeklyreport)) {
            echo $this->Form->control('title', array('value' => $current_weeklyreport['title']));
            ?>
            <div style="display: flex; justify-content: flex-start;">
            <?php
            echo $this->Form->control('week', array('value' => $current_weeklyreport['week'], 'label' => 'For week', 'type' => 'number', 'min' => 1, 'max' => 52));
            echo $this->Form->control('year', array('value' => $current_weeklyreport['year'], 'min' => $now->year-1, 'max' => $now->year));
            echo $this->Form->control('meetings', array('value' => $current_weeklyreport['meetings'], 'type' => 'number', 'min' => 0));
            echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'Number of meetings on this week.', 'class' => 'infoicon', 'style' => 'align-self: center;']);
            ?>
            </div>
            <?php
            echo $this->Form->control('problems', array('value' => $current_weeklyreport['problems'], 'label' => 'Challenges, issues, etc.'));
            echo $this->Form->control('additional', array('value' => $current_weeklyreport['additional'], 'label' => 'Additional information'));
        } else {
            $currProj = $this->request->getSession()->read('selected_project')['project_name'];

            echo $this->Form->control('title', array('value' => $currProj.', weekly report'));
            ?>
            <div style="display: flex; justify-content: flex-start;">
            <?php
            // the week and year for the last weeklyreport of the year are not automatically filled out
            if ($nowWeek == 01) {
                echo $this->Form->control('week', array('label' => 'For week', 'type' => 'number', 'min' => 1, 'max' => 52));
                echo $this->Form->control('year', array('min' => $now->year-1, 'max' => $now->year));
            } else {
                echo $this->Form->control('week', array('label' => 'For week', 'value' => $reportWeek, 'type' => 'number', 'min' => 1, 'max' => 52));
                echo $this->Form->control('year', array('value' => $now->year, 'min' => $now->year-1, 'max' => $now->year));
            }
                
            echo $this->Form->control('meetings', array('type' => 'number', 'min' => 0));
            echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'Number of meetings on this week.', 'class' => 'infoicon', 'style' => 'align-self: center;']);
            ?>
            </div>
            <?php
            echo $this->Form->control('problems', array('label' => 'Challenges, issues, etc.'));
            echo $this->Form->control('additional', array('label' => 'Additional information'));
        }
            echo $this->Form->button(__('Next page'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
<style>
   .infoicon{float: right; margin-top: 31px; margin-left: 10px;}
</style>