<div class="workinghours index large-9 medium-18 columns content float: left">
    <h3><?= __('Project Risks') ?></h3>
    <?php
            $admin = $this->request->getSession()->read('is_admin');
            $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            $senior_developer = ( $this->request->getSession()->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
            $developer = ( $this->request->getSession()->read('selected_project_role') == 'developer' ) ? 1 : 0;
            // link not visible to supervisors and clients
    if ($admin || $senior_developer) {
        ?>
            <!-- This button has been changed to link because of the accessibility.-->
            <div id="navbutton"><?= $this->Html->link(__('+ New risk'), ['action' => 'add']) ?></div>
            <?php
    }
            // link not visible to devs and clients
    if ($admin || $senior_developer) {
        ?>
    <?php } ?>
    <?php // the code for the menu is the same as in adddev.ctp
    //echo $this->Form->control('member_id', ['options' => $members, 'label' => 'Show hours for', 'empty' => '']) . $this->Form->button(__('Submit'));
    ?>
    <table id="risks-table" cellpadding="0" cellspacing="0" class="scrollable-table">
        <thead>
            <tr>
                <th style="min-width:100px;"><?= __('Risk') ?></th>
                <th style="min-width:100px;" id="th-column-cause"><?= __('Cause') ?>
                    <button type="button" data-column="#th-column-cause" class="expand-button">+</button>
                </th>
                <th style="min-width:130px;" id="th-column-mitigation"><?= __('Mitigation') ?>
                    <button type="button" data-column="#th-column-mitigation" class="expand-button">+</button>
                </th>
                <th style="min-width:100px;"><?= __('Category') ?></th>
                <th style="min-width:75px;"><?= __('Severity') ?></th>
                <th style="min-width:80px;"><?= __('Probability') ?></th>
                <th style="min-width:80px;"><?= __('Importance') ?></th>
                <th style="min-width:75px;"><?=__('Impact') ?></th>
                <th style="min-width:75px;"><?=__('Status') ?></th>
                <th style="min-width:150px;"><?=__('Times Realized') ?></th>
                <th style="min-width:70px;" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($risks as $risk) : ?>
            <tr>
                <td ><?= $risk->description ?></td>
                <td class="shortened shortenable"><?= $risk->cause ?></td>
                <td class="shortened shortenable"><?= $risk->mitigation ?></td>
                <td><?= $categories[$risk->category] ?></td>
                <td><?= $types[$risk->severity] ?></td>
                <td><?= $types[$risk->probability] ?></td>
                <td><?= $types[min(5, floor(($risk->severity * $risk->probability) / 5) + 1)] ?></td>
                <td><?= $impactTypes[$risk->impact] ?></td>
                <td><?= $statusTypes[$risk->status] ?></td>
                <td><?= $risk->realizations ?></td>
                <td class="actions">
                    <?php
                    $admin = $this->request->getSession()->read('is_admin');
                    $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
                    $senior_developer = ( $this->request->getSession()->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;

                    if ($senior_developer || $supervisor ||  $admin) { ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $risk->id]) ?><br/>
                    <?php }
                    if (($senior_developer || $supervisor ||  $admin) && $deletable[$risk->id]) { ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $risk->id], ['confirm' => __('Are you sure you want to delete # {0}?', $risk->id)]) ?> 
                    <?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
