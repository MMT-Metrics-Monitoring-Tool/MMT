

<div class="metrics form large-8 medium-16 columns content float: left">
    <h3><?= __('Edit Metric: ' . $metric['description']) ?></h3>
    <div id="navbutton">
        <?= $this->Form->postLink(
            __('Delete Metric'),
            ['action' => 'delete', $metric->id],
            ['confirm' => __('Are you sure you want to delete # {0}?', $metric->id)]
        )
        ?>
    </div>
    <?php
        $admin = $this->request->getSession()->read('is_admin');
    if ($admin) {
        ?>
            <div id="navbutton">
            <?= $this->Form->postLink(
                __('Delete (admin)'),
                ['action' => 'deleteadmin', $metric->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $metric->id)]
            )
            ?>
            </div>
        <?php
    }
    ?> 
    <?= $this->Form->create($metric) ?>
        <?php
            echo $this->Form->control('value', array('type' => 'number', 'min' => 0, 'style' => 'width: 30%;'));
            echo $this->Form->button(__('Submit'));
        ?>
    <?= $this->Form->end() ?>
</div>
