
<div class="workinghours view large-7 medium-14 columns content float: left">
    <h3><?= h("View logged task") ?></h3>
    <?php
        $admin = $this->request->getSession()->read('is_admin');
        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
        $senior_developer = ( $this->request->getSession()->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
        // the week and year of the last weekly report
        $project_id = $this->request->getSession()->read('selected_project')['id'];
    if (($workinghour->member->user_id == $this->request->getSession()->read('Auth.User.id')) || $senior_developer || $supervisor || $admin) { ?>
                <div id="navbutton"><?= $this->Html->link(__('Edit logged time'), ['action' => 'edit', $workinghour->id]) ?> </div>
    <?php } ?>
    <table class="vertical-table">
        <tr>
            <th><?= __('Member') ?></th>
            <td colspan="2">
                <?php $name_role = str_replace('_', ' ', $workinghour->member->member_name) ?>
                <?=
                        $workinghour->has('member') ? $this->Html->link($name_role, ['controller' => 'Members', 'action' => 'view', $workinghour->member->id]) : ''
                ?> 
            </td>
        </tr>
        <tr>
            <th><?= __('Date') ?></th>
            <td colspan="2"><?= h($workinghour->date->format('d.m.Y')) ?></tr>
        </tr>
        <tr>
            <th><?= __('Duration') ?></th>
            <td colspan="2"><?= $this->Number->format($workinghour->duration) ?></td>
        </tr>
        <tr>
            <th><?= __('Worktype') ?></th>
            <td colspan="2"><?= $workinghour->has('worktype') ? $this->Html->link($workinghour->worktype->description, ['controller' => 'Worktypes', 'action' => 'view', $workinghour->worktype->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Description') ?></th>
            <td colspan="2"><?= h(wordwrap($workinghour->description, 35, "\n", true)) ?></td>
        </tr>        
        <?php if ($workinghour->created_on != null) {?>
            <tr>
                <th><?= __('Created On') ?></th>
                <td colspan="2"><?= h($workinghour->created_on->format('d.m.Y')) ?></tr>
            </tr>
        <?php } ?>
        <?php if ($workinghour->modified_on != null) {?>
            <tr>
                <th><?= __('Updated On') ?></th>
                <td colspan="2"><?= h($workinghour->modified_on->format('d.m.Y')) ?></tr>
            </tr>
        <?php } ?>
    </table>
</div>
