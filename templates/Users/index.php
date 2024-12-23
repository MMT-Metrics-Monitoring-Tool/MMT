
<div class="users index large-9 medium-8 columns content float: left">
    <h3><?= __('Users') ?></h3>
        <!--This button has been changed to link because of the accessibility.-->
        <div id="navbutton"><?= $this->Html->link(__('+ New User'), ['action' => 'add']) ?></div>
    <!--</button>-->
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <!--
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('email') ?></th>
                -->
                <th><?= $this->Paginator->sort('first_name') ?></th>
                <th><?= $this->Paginator->sort('last_name') ?></th>
                <th><?= $this->Paginator->sort('role') ?></th>
                <th><?= $this->Paginator->sort('research_permission') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
            <tr>
                <!--
                <td><?= $this->Number->format($user->id) ?></td>
                <td><?= h($user->email) ?></td>
                -->
                <td><?= h($user->first_name) ?></td>
                <td><?= h($user->last_name) ?></td>
                <td><?= h($user->role) ?></td>
                <td><?php if ($user->research_allowed == 1) {
                        echo ("Allowed");
                    } else if ($user->research_allowed == 0) {
                        echo ("Disallowed");
                    } else if ($user->research_allowed == -1) {
                        echo ("No answer");
                    } else {
                        echo ("No answer");
                    } ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $user->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $user->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
