
<div class="metrics index large-8 medium-16 columns content float: left">
    <h3><?= __('Metrics') ?></h3>
    <?php
        $admin = $this->request->getSession()->read('is_admin');
        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
               
    if ($admin) {
        ?>
        <!--This button has been changed to link because of the accessibility.-->
        <div id="managing_button"><?= $this->Html->link(__('New Metric (admin)'), ['action' => 'addadmin']) ?></div>
    <?php }
    if ($admin || $supervisor) {
        ?> 
    <!--This button has been changed to link because of the accessibility.-->
        <div id="navbutton"><?= $this->Html->link(__('New Metric'), ['action' => 'add']) ?></div>
        <?php
    }
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('metrictype_id') ?></th>
                <th><?= $this->Paginator->sort('value') ?></th>
                <th><?= $this->Paginator->sort('weeklyreport_id') ?></th>
                <th><?= $this->Paginator->sort('date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metrics as $metric) : ?>
            <tr>
                <td><?= $metric->has('metrictype') ? $this->Html->link($metric->metrictype->description, ['controller' => 'Metrictypes', 'action' => 'view', $metric->metrictype->id]) : '' ?></td>
                <td><?= $this->Number->format($metric->value) ?></td>
                <td><?= $metric->has('weeklyreport') ? $this->Html->link($metric->weeklyreport->title, ['controller' => 'Weeklyreports', 'action' => 'view', $metric->weeklyreport->id]) : '' ?></td>
                <td><?= h($metric->date->format('d.m.Y')) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $metric->id]) ?>
                    <?php
                        $admin = $this->request->getSession()->read('is_admin');
                        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
                    if ($admin || $supervisor) {
                        ?>
            
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $metric->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $metric->id], ['confirm' => __('Are you sure you want to delete # {0}?', $metric->id)]) ?>
                    <?php } ?>
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
