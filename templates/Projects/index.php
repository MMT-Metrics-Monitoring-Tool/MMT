

<div class="projects index large-9 medium-18 columns content float: left">
    <!-- List of the projects the user is a member of -->
    <?php if ($this->request->getSession()->check('Auth.User')) { ?>       
        <h3><?= __('My projects') ?></h3>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="2"><?= $this->Paginator->sort('project_name') ?></th>
                    <th><?= $this->Paginator->sort('created_on', ['label' => 'Starting Date']) ?> </th>
                    <th><?= __('Description') ?></th>
                    <?php // links to unread weekly reports for supervisors
                    $admin = $this->request->getSession()->read('is_admin');
                    $super = $this->request->getSession()->read('is_supervisor');
                    if ($admin || $super) { ?>
                        <th colspan="2"><?= __('Unread Weekly Reports') ?></th>
                    <?php } ?>
                   <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project) : ?>
                    <?php if (in_array($project->id, $this->request->getSession()->read('project_memberof_list'))) { ?>
                        <tr>    
                            <td colspan="2"><?= $project->has('project_name') ? $this->Html->link($project->project_name, ['action' => 'view', $project->id]) : '' ?></td>
                            <td><?= h(date_format($project->created_on, "j.n.Y")) ?></td>
                            <td><?= h($project->description) ?></td>
                            <?php
                            // Links to unread weeklyreports are visible to supervisors
                            // admin can only see the column (no links)
                            // code is the same as in Statistics.ctp
                            if ($admin || $super) {
                                $userid = $this->request->getSession()->read('Auth.User.id');
                                $query = Cake\ORM\TableRegistry::get('Weeklyreports')
                                ->find()
                                ->select()
                                ->where(['project_id =' => $project['id']])
                                ->toArray(); ?> 
                            <td colspan="2"> 
                                <?php foreach ($query as $key) {
                                    $reportId = $key->id;
                                 
                                    $newreps = Cake\ORM\TableRegistry::get('Newreports')->find()
                                    ->select()
                                    ->where(['user_id =' => $userid, 'weeklyreport_id =' => $reportId])
                                    ->toArray();
                                    if (sizeof($newreps) > 0) { ?>                          
                                        <?= $this->Html->link(__('Week ' . $key->week), ['controller' => 'Weeklyreports', 'action' => 'view', $reportId]) ?>    
                                    <?php }
                                }?> 
                            </td>
                            <?php } ?>
                            <td class="actions">
                                <?= $this->Html->link(__('Select'), ['action' => 'view', $project->id]) ?>
                            </td>      
                        </tr>
                    <?php } ?>
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
    <?php } ?>

</div>
