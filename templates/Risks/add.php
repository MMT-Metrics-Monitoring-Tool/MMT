
<!-- 
    If the lower navigation bar needed, links go here
    
    <ul class="side-nav">
        
    </ul>
-->

<div class="workinghours form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($risk) ?>
    <fieldset>
        <legend><?= __('Add new risk') ?></legend>
        
        <?php
            echo $this->Form->control('description');
            echo $this->Form->control('cause');
            echo $this->Form->control('mitigation');
            echo $this->Form->control('severity', ['options' => $types, 'empty' => ' ', 'required' => true]);
            echo $this->Form->control('category', ['options' => $categories, 'empty' => ' ', 'required' => true]);
            echo $this->Form->control('probability', ['options' => $types, 'empty' => ' ', 'required' => true]);
            echo $this->Form->control('impact', ['options' => $impactTypes, 'empty' => ' ', 'required' => true ]);
            echo $this->Form->control('status', ['options' => $statusTypes, 'empty' => ' ', 'required' => true ]);
         
            $project_id = $this->request->getSession()->read('selected_project')['id'];
          
        echo $this->Form->button(__('Submit'));
        ?>    
    </fieldset>
    <?= $this->Form->end() ?>
</div>
