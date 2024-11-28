
<div class="workinghours form large-8 medium-16 columns content float: left">
    <h3><?= __('Edit risk') ?></h3>
    <?= $this->Form->create($risk) ?>
        <?php
        echo $this->Form->control('description');
        echo $this->Form->control('cause');
        echo $this->Form->control('mitigation');

        //if ($deletable) {
        echo $this->Form->control('severity', ['options' => $types, 'empty' => ' ', 'required' => true]);
        echo $this->Form->control('category', ['options' => $categories, 'empty' => ' ', 'required' => true]);
        echo $this->Form->control('probability', ['options' => $types, 'empty' => ' ', 'required' => true]);
        echo $this->Form->control('impact', ['options' => $impactTypes, 'empty' => ' ', 'required' => true ]);
        echo $this->Form->control('status', ['options' => $statusTypes, 'empty' => ' ', 'required' => true ]);
        //}
        
        $project_id = $this->request->getSession()->read('selected_project')['id'];
          
        echo $this->Form->button(__('Submit'));
        ?>
    <?= $this->Form->end() ?>
</div>
