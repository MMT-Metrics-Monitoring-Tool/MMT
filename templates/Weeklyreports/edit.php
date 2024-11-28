
<div class="weeklyreports form large-8 medium-16 columns content float: left">
    <h3><?= __('Edit Weeklyreport') ?></h3>
    <div id="navbutton">
        <?= $this->Form->postLink(
            __('Delete'),
            ['action' => 'delete', $weeklyreport->id],
            ['confirm' => __('Are you sure you want to delete # {0}?', $weeklyreport->id)]
        )
        ?>
    </div>

    <?= $this->Form->create($weeklyreport) ?>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('week', array('type' => 'number', 'min' => 1, 'max' => 52, 'style' => 'width: 40%;'));
            echo $this->Form->control('year', array('style' => 'width: 50%;'));
            echo $this->Form->control('meetings', array('type' => 'number', 'min' => 0, 'style' => 'width: 40%;'));
            //echo $this->Form->control('reglink', ['label' => 'Requirements link']);
            echo $this->Form->control('problems', array('label' => 'Challenges, issues, etc.'));
            echo $this->Form->control('additional');
            echo $this->Form->button(__('Submit'));
        ?>
    <?= $this->Form->end() ?>
</div>
