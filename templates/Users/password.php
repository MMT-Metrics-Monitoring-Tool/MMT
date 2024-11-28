
<div class="users form large-8 medium-16 columns content float: left">
    <h3><?= __('Change password') ?></h3>
    <div id="navbutton"><?= $this->Html->link(__('Edit profile'), ['action' => 'editprofile']) ?></div>
    <?= $this->Form->create($user) ?>
        <?php
            echo $this->Form->control('password', ['label' => 'New password', 'value' => '', 'id' => 'key', 'empty']);
            echo $this->Form->control('checkPassword', array('label' => 'Retype the new password', 'value' => '', 'required' => true, 'type' => 'password', 'empty'));

            echo $this->Form->button(__('Submit'));
            
        ?>
    <?= $this->Form->end() ?>
</div>
