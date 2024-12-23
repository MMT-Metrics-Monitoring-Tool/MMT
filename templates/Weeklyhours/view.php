
<div class="weeklyhours view large-7 medium-14 columns content float: left">
    <h3><?= h("View weeklyhour") ?></h3>
    <div id="navbutton"><?= $this->Html->link(__('Edit Weeklyhour'), ['action' => 'edit', $weeklyhour->id]) ?></div>
    <table class="vertical-table">
        <tr>
            <th><?= __('Weeklyreport') ?></th>
            <td><?= $weeklyhour->has('weeklyreport') ? $this->Html->link($weeklyhour->weeklyreport->title, ['controller' => 'Weeklyreports', 'action' => 'view', $weeklyhour->weeklyreport->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Member') ?></th>
            <td><?= $weeklyhour->has('member') ? $this->Html->link($weeklyhour->member->member_name, ['controller' => 'Members', 'action' => 'view', $weeklyhour->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($weeklyhour->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Duration') ?></th>
            <td><?= $this->Number->format($weeklyhour->duration) ?></td>
        </tr>
    </table>
</div>
