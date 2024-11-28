
<div class="workinghours index large-9 medium-18 columns content float: left">
    <?php // member name for the header
    foreach ($workinghours as $workinghour) {
        foreach ($memberlist as $member) {
            if ($workinghour->member->id == $member['id']) {
                $workinghour->member['member_name'] = $member['member_name'];
            }
        }
    }
    foreach ($this->request->getParam('pass') as $var) {
        $id = $var;
    }?>
    <h3>
        <?php $name_role = str_replace('_', ' ', $workinghour->member['member_name']) ?>
        <?= $this->Html->link(__($name_role), ['controller' => 'Members', 'action' => 'view', $id]) ?>
    </h3>
    <div class="related">
    <h4><?= __('Logged tasks') ?></h4>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:75px;"><?= $this->Paginator->sort('date') ?></th>
                <th style="width:60px;"><?= __('Week') ?></th>
                <th colspan="2"><?= __('Description') ?></th>
                <th style="width:65px;"><?= $this->Paginator->sort('duration') ?></th>
                <th><?= $this->Paginator->sort('worktype_id') ?></th>
                <th style="width:70px;" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($workinghours as $workinghour) : ?>
            <tr>
                <?php /*
                    foreach($memberlist as $member){
                        if($workinghour->member->id == $member['id']){
                           $workinghour->member['member_name'] = $member['member_name'];
                        }
                    } */
                ?>
                <td><?= h($workinghour->date->format('d.m.Y')) ?></td>
                <td style="text-align: center;"><?= h($workinghour->date->format('W')) ?></td>
                <td colspan="2" style="font-family:monospace;"><?= h(wordwrap($workinghour->description, 28, "\n", true)) ?></td>
                <td style="text-align: center;"><?= $this->Number->format($workinghour->duration) ?></td>  
                <td><?= h($workinghour->worktype->description) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $workinghour->id]) ?>
                    <?php
                    $admin = $this->request->getSession()->read('is_admin');
                    $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
                    $senior_developer = ( $this->request->getSession()->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
         
                    $week= $workinghour->date->format('W');
                    $year= $workinghour->date->format('Y');

                    $firstWeeklyReport = false;

                    $project_id = $this->request->getSession()->read('selected_project')['id'];
            
                    // the week and year of the last weekly report
                    $query = Cake\ORM\TableRegistry::get('Weeklyreports')
                    ->find()
                    ->select(['year','week'])
                        ->where(['project_id =' => $project_id])
                        ->order(['year' => 'DESC', 'week' => 'DESC'])
                        ->limit(1)
                        ->toArray();
                
                    if ($query != null) {
                        $maxYear = $query[0]['year'];
                        $maxWeek = $query[0]['week'];
                    } else {
                        $firstWeeklyReport = true;
                    }

                    $allowedToEdit = false;

                    // edit and delete are only shown if the weekly report is not sent
                    if (($firstWeeklyReport || $year == $maxYear) && ($week > $maxWeek || $year > $maxYear)) {
                        $allowedToEdit = true;
                    }

                    // edit and delete can also be viewed by the developer who owns them
                    if (($workinghour->member->user_id == $this->request->getSession()->read('Auth.User.id') && $allowedToEdit) ||
                        $senior_developer || $admin || $supervisor) { ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $workinghour->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $workinghour->id], ['confirm' => __('Are you sure you want to delete # {0}?', $workinghour->id)]) ?> 
                    <?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
