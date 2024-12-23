
<div class="notes form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($note) ?>
    <fieldset>
        <legend><?= __('Got questions or feedback? Found a bug?') ?></legend>
        <?php
            echo $this->Form->control(
                'content',
                array('label' => 'Write your note here', 'class' => 'feedback', 'type' => 'textarea')
            );
            echo $this->Form->control('contact_user', ['type' => 'checkbox', 'label' => 'Would you like a reply?']);
            echo $this->Form->button(__('Submit'));
            ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>