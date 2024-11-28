
<div class="users view large-7 medium-14 columns content float: left">
    <?= $rooli = "";
if ($user->role == 'inactive') {
$rooli = "(inactive)";
}?>
    <h3><?= h($user->first_name ." ". $user->last_name . " ". $rooli) ?></h3>
    <?php   $admin = $this->request->getSession()->read('is_admin');
            
    if ($admin) { ?>
            <!-- This button has been changed to link because of the accessibility.-->
            <div id="navbutton"><?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id]) ?></div>
    <?php } ?>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th><?= __('Role') ?></th>
            <td><?= h($user->role) ?></td>
        </tr>
        <tr>
            <th><?= __('') ?></th>
            <td>
                <?php if ($user->research_allowed == 1) {
                        echo ("");
                } else if ($user->research_allowed == 0) {
                    echo ("");
                } else if ($user->research_allowed == -1) {
                    echo ("");
                } else {
                    echo ("");
                } ?>            
            </td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Member of') ?></h4>
        <?php if (!empty($user->members)) : ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Project') ?></th>
                <th><?= __('Project role') ?></th>
                <th><?= __('Starting date') ?></th>
                <th><?= __('Ending date') ?></th>
            </tr>
            <?php foreach ($user->members as $members) : ?>
            <tr>
                <td><?php
                    $query = Cake\ORM\TableRegistry::get('Projects')
                            ->find()
                            ->select(['project_name'])
                            ->where(['id =' => $members->project_id])
                            ->toArray();
                    echo $query[0]->project_name;
                ?></td>
                <td><?php
                    $current_role = array('client' => 'customer', 'developer' => 'developer', 'senior_developer' => 'senior developer', 'supervisor' => 'coach',
                    'user' => 'user', 'admin' => 'admin', 'inactive' => 'inactive');
                    echo h($current_role[$members->project_role]);
                    ?></td>
                <td><?php
                if ($members->starting_date != null) {
                    echo h($members->starting_date->format('d.m.Y'));
                }
                ?></td>
                <td><?php
                if ($members->ending_date != null) {
                    echo h($members->ending_date->format('d.m.Y'));
                }
                ?></td>
                
                <!-- Unable to do these because only members of currently chosen project can be accessed
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Members', 'action' => 'view', $members->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Members', 'action' => 'edit', $members->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Members', 'action' => 'delete', $members->id], ['confirm' => __('Are you sure you want to delete # {0}?', $members->id)]) ?>

                </td>
                -->
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
