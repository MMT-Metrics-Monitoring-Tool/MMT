
<div class="users form large-8 medium-16 columns content float: left">
    <h3>Edit profile</h3>
    <?= $this->Form->create($user) ?>
            <?php
                echo $this->Form->control('email');
                echo $this->Form->control('first_name');
                echo $this->Form->control('last_name');
            ?>
            <button>Submit</button>
    <?= $this->Form->end() ?>
</div>
<div class="users form large-8 medium-16 columns content float: left">
    <?= $this->Form->create() ?>
        <h3><?= __('Reset Password') ?></h3>
        <?php
            echo $this->Form->control('password', ['label' => 'New Password','value' => '', 'type' => 'password', 'required' => true,'empty', 'placeholder' => 'The password has to be at least 8 characters long']);
        ?>
        <button>Submit</button>
    <?= $this->Form->end(); ?>
</div>
