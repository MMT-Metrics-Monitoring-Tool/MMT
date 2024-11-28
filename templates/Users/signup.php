
<div class="users form large-8 medium-16 columns content float: left">
    <h3><?= __('Create User') ?></h3>
    <?= $this->Form->create($user) ?>
        <?php
            echo $this->Form->control('email');
            echo $this->Form->control('password', array('placeholder' => 'The password has to be at least 8 characters long'));
            echo $this->Form->control('first_name');
            echo $this->Form->control('last_name');
            echo $this->Form->control('checkIfHuman', array('label' => 'Write the sum of 2 + 3', 'required' => true));
            echo $this->Form->button(__('Submit'));
        ?>
    <?= $this->Form->end(); ?>
    <p style="margin-top: 6em">
    User information is stored according to the Privacy notice.
    </p>
</div>