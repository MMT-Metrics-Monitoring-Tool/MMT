

<div class="users form large-8 medium-16 columns content float: left">
    <h3><?= __('Edit User') ?></h3>
    <div id="navbutton">
        <?= $this->Form->postLink(
            __('Delete'),
            ['action' => 'delete', $user->id],
            ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]
        )
        ?>
    </div>
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
