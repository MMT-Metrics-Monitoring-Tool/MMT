
<div class="users form large-8 medium-16 columns content float: left">
    <h3><?= __('Add User') ?></h3>
    <?= $this->Form->create($user) ?>
        <?php
            echo $this->Form->control('email');
            echo $this->Form->control('password');
            echo $this->Form->control('first_name');
            echo $this->Form->control('last_name');
            echo $this->Form->control(
                'role',
                ['options' => array('user' => 'user', 'admin' => 'admin', 'inactive' => 'inactive')]
            );
            echo $this->Form->button(__('Submit'));
            ?>
    <?= $this->Form->end() ?>
</div>
