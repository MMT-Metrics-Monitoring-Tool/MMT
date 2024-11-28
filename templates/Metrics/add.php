
<!-- 
    If the lower navigation bar needed, links go here
    <ul class="side-nav">  
    </ul>
-->

<div class="metrics form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($metric) ?>
    <fieldset>
        <legend><?= __('Add Metric') ?></legend>
        <?php
            echo $this->Form->control('metrictype_id', ['options' => $metrictypes]);
            echo $this->Form->control('date');
            echo $this->Form->control('value', array('style' => 'width: 30%;'));
            echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
