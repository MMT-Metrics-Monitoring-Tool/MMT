
<div class="workinghours form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($risk) ?>
    <fieldset>
        <legend><?= __('Edit risk: ' . $this->request->getSession()->read('selected_risk_description')) ?></legend>        
        <?php
            echo $this->Form->control('severity', ['options' => $types, 'empty' => ' ', 'required' => true]);
            echo $this->Form->control('probability', ['options' => $types, 'empty' => ' ', 'required' => true]);
            echo $this->Form->button(__('Submit'));
        ?>    
    </fieldset>
    <?= $this->Form->end() ?>

</div>