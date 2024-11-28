<?php
    $userid = $this->request->getSession()->read('Auth.User.id');
    $projid = $this->request->getSession()->read('selected_project')['id'];
    $wrid = $weeklyreport->id;

    // fetch member id of current user in currently chosen project
    $memid = Cake\ORM\TableRegistry::get('Members')->find()
                ->select(['id'])
                ->where(['user_id =' => $userid, 'project_id =' => $projid])
                ->toArray();
    
        $connection = \Cake\Datasource\ConnectionManager::get("default");
        
if (!empty($memid[0]->id)) {
    $memid = $memid[0]->id;

    // if current weeklyreport's ID is in notifications, remove the row where current member's id is
            $connection->delete('notifications', ['member_id' => $memid, 'weeklyreport_id' => $wrid]);
}
        // let's also remove data about unread weeklyreports
        $supervisor = ($this->request->getSession()->read('selected_project_role') == 'supervisor') ? 1 : 0;
        $super = $this->request->getSession()->read('is_supervisor');
        //if ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) {
if ($super || $supervisor) {
    $newreps = Cake\ORM\TableRegistry::get('Newreports')->find()
    ->select()
    ->where(['user_id =' => $userid, 'weeklyreport_id =' => $wrid])
    ->toArray();
    if (sizeof($newreps) > 0) {
                    $connection->delete('newreports', ['user_id' => $userid, 'weeklyreport_id' => $wrid]);
    }
}
    
    // if you're an admin or supervisor, we'll force you to change to the project the weeklyreport is from
    $admin = $this->request->getSession()->read('is_admin');
    $senior_developer = ( $this->request->getSession()->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
        $developer = ( $this->request->getSession()->read('selected_project_role') == 'developer' ) ? 1 : 0;

if ($admin || $supervisor) {
    // fetch the ID of relevant project
    $query = Cake\ORM\TableRegistry::get('Weeklyreports')
                ->find()
                ->select(['project_id'])
                ->where(['id =' => $weeklyreport['id']])
                ->toArray();
    $iidee = $query[0]->project_id;
        
    /* This code is a modified copy of Projects-controller's view-function.
     * Essentially it is an unnecessary copy, but it cannot be accessed directly because MVC doesn't
     * allow using controllers inside other controllers.
     */
    $project = Cake\ORM\TableRegistry::get('Projects')->get($iidee, [
        'contain' => ['Members', 'Metrics', 'Weeklyreports']
    ]);
    $this->set('project', $project);
    $this->set('_serialize', ['project']);
        
    // if the selected project is a new one
    if ($this->request->getSession()->read('selected_project')['id'] != $project['id']) {
        // write the new id
        $this->request->getSession()->write('selected_project', $project);
        // remove the all data from the weeklyreport form if any exists
        $this->request->getSession()->delete('current_weeklyreport');
        $this->request->getSession()->delete('current_metrics');
        $this->request->getSession()->delete('current_weeklyhours');
    }
}

    $creatorQuery = Cake\ORM\TableRegistry::get('Users')->find()
            ->select(['first_name', 'last_name'])
            ->where(['id =' => $weeklyreport->created_by])
            ->toArray();

    $created_by = "";
if ($creatorQuery != null) {
    $created_by = $creatorQuery[0]->first_name ." ". $creatorQuery[0]->last_name;
}
    
    $updaterQuery = Cake\ORM\TableRegistry::get('Users')->find()
            ->select(['first_name', 'last_name'])
            ->where(['id =' => $weeklyreport->updated_by])
            ->toArray();

    $updated_by = "";
if ($updaterQuery != null) {
    $updated_by = $updaterQuery[0]->first_name ." ". $updaterQuery[0]->last_name;
}
    
    
?>

<div class="weeklyreports view large-8 medium-16 columns content float: left">
    <h3><?= h($weeklyreport->title) ?></h3>
    <?php
    if ($admin || $supervisor || $senior_developer) { ?>
        <!--This button has been changed to link because of the accessibility.-->
        <div id="navbutton"><?= $this->Html->link(__('Edit Weeklyreport'), ['action' => 'edit', $weeklyreport->id]) ?></div>
    <?php } ?>
    <h5><?= h($selected_project = $this->request->getSession()->read('selected_project')['project_name']) ?></h5>
    <table class="vertical-table">
        <tr>
            <th><?= __('Title') ?></th>
            <td><?= h($weeklyreport->title) ?></td>
        </tr>
        <tr>
            <th><?= __('Week') ?></th>
            <td><?= h($weeklyreport->week) ?></tr>
        </tr>
        <tr>
            <th><?= __('Year') ?></th>
            <td><?= h($weeklyreport->year) ?></tr>
        </tr>
        <tr>
            <th><?= __('Meetings') ?></th>
            <td><?= h($weeklyreport->meetings) ?></td>
        </tr>
        <tr>
            <th><?= __('Challenges, issues, etc.') ?></th>
            <td><?= h($weeklyreport->problems) ?></td>
        </tr>
        <tr>
            <th><?= __('Additional') ?></th>
            <td><?= h($weeklyreport->additional) ?></td>
        </tr>
        <tr>
            <th><?= __('Created by') ?></th>
            <td><?= h($created_by) ?></td>
        </tr>
        <tr>
            <th><?= __('Created on') ?></th>
            <td><?= h($weeklyreport->created_on->format('d.m.Y')) ?></td>
        </tr>
        <tr>
            <th><?= __('Updated by') ?></th>
            <td><?= h($updated_by) ?></td>
        </tr>
        <tr>
            <th><?= __('Updated on') ?></th>
        <td><?php
        if ($weeklyreport->updated_on != null) {
            echo h($weeklyreport->updated_on->format('d.m.Y'));
        } ?></td>
    </table>
    <div class="related">
        <h4><?= __('Working hours for week ') . $weeklyreport->week ?></h4>
        
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th colspan="2"><?= __('Name') ?></th>
                <th><?= __('Project role') ?></th>                    
                <th><?= __('Working hours') ?></th>
            </tr>

            
            <?php
            // Finding members who are not supervisors or clients
            $p_id = $this->request->getSession()->read('selected_project')['id'];
            $mlist = Cake\ORM\TableRegistry::get('Members')->find()
                ->select()
                ->where([
                    'project_id =' => $p_id,
                    'OR' => [
                        ['project_role =' => 'developer'],
                        ['project_role =' => 'senior_developer']
                    ]
                ])
                ->toArray();
            
            foreach ($mlist as $member) : ?>
                <tr>
                <?php
                $m_id = $member->id;
                $u_id = $member->user_id;
                $queryForHours = Cake\ORM\TableRegistry::get('Workinghours')->find()
                        ->select()
                        ->where(['member_id =' => $m_id])
                        ->toArray();
                $queryForName = Cake\ORM\TableRegistry::get('Users')->find()
                        ->select(['first_name', 'last_name'])
                        ->where(['id =' => $u_id])
                        ->toArray();
                $sum = 0;
                if (!empty($queryForHours)) {
                    $hours = array();
                    // Get member's hours for the week
                    foreach ($queryForHours as $key) {
                        if ($weeklyreport->week == $key->date->format('W')) {
                            if (($weeklyreport->week == 52 && $key->date->format('m') == 01) ||
                                    ($weeklyreport->week == 5 && $key->date->format('m') == 01) ||
                                    ($weeklyreport->week == 1 && $key->date->format('m') == 12) ||
                                    ($weeklyreport->year == $key->date->format('Y'))) {
                                    $hours[] = $key->duration;
                                    $sum = array_sum($hours);
                            }
                        }
                    }
                }
                // finding member's full name
                if (!empty($queryForName)) {
                    foreach ($queryForName as $name) {
                        $fullName = $name->first_name . " " . $name->last_name;
                    }
                }?>
            
                <td colspan="2"><?= h($fullName) ?></td>
                <td>
                    <?php
                        $member_role = array('client' => 'customer', 'developer' => 'developer', 'senior_developer' => 'senior developer');
                        echo $member_role[$member->project_role];
                    ?>
                </td> 
                <td><?= h($sum) ?></td> 
            
        </tr>
            <?php endforeach; ?>
        </table>
        
        
        <h4><?= __('Metrics') ?></h4>
            <?php if (!empty($weeklyreport->metrics)) : ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th colspan="2"><?= __('Metrictype') ?></th>                 
                    <th><?= __('Value') ?></th>
                    <th><?= __('Date') ?></th>
                    <?php /*
                    $queryForMax = Cake\ORM\TableRegistry::get('Weeklyreports')
                        ->find()
                        ->select(['year', 'week'])
                        ->where(['project_id =' => $weeklyreport['project_id']])
                        ->toArray();
                    if(!empty($queryForMax)) {
                        $lastReport = max($queryForMax);
                    }
                    */
                    // if (($admin || $supervisor) || ($senior_developer && (($weeklyreport->year >= $lastReport['year']) && ($weeklyreport->week >= $lastReport['week'])))) {
                    if ($admin || $supervisor || $senior_developer) { ?> 
                        <th class="actions"><?= __('Actions') ?></th>
                    <?php } ?>
                </tr>
                <?php foreach ($weeklyreport->metrics as $metrics) : ?>
                <tr>
                    <td colspan="2"><?= h($metrics->metric_description) ?></td>
                    <?php
                    if ($metrics->metric_description == "Overall status") {
                        if ($metrics->value == 1) {
                            $overall_status = 'All OK';
                        } else if ($metrics->value == 2) {
                            $overall_status = 'Minor issues';
                        } else if ($metrics->value == 3) {
                            $overall_status = 'Serious problems';
                        }
                    } else {
                        $overall_status = $metrics->value;
                    }
                    ?>
                    <td><?= h($overall_status) ?></td>
                    <td><?= h($metrics->date->format('d.m.Y')) ?></td>                  
                    <?php
                    // admins and supervisors can edit metrics
                    // seniordevelopers can edit metrics of the last weeklyreport
                    // if (($admin || $supervisor) || ($senior_developer && (($weeklyreport->year >= $lastReport['year']) && ($weeklyreport->week >= $lastReport['week'])))) {
                    if ($admin || $supervisor || $senior_developer) { ?>     
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['controller' => 'Metrics', 'action' => 'edit', $metrics->id]) ?>  
                        </td> 
                    <?php } ?>
                </tr>
                <?php endforeach; ?>
            </table>
        
            <?php endif; ?>
        
        
        <?php if (!empty($risks)) : ?>
        <h4><?= __('Risks') ?></h4>
        
        <table cellpadding="0" cellspacing="0" class="scrollable-table">
                <tr>
                    <th colspan="2"><?= __('Risk') ?></th>     
                    <th id="th-column-cause"><?= __('Cause') ?>
                        <button type="button" data-column="#th-column-cause" class="expand-button">+</button>
                    </th>
                    <th id="th-column-mitigation"><?= __('Mitigation') ?>
                        <button type="button" data-column="#th-column-mitigation" class="expand-button">+</button>
                    </th>
                    <th><?= __('Severity') ?></th>               
                    <th><?= __('Probability') ?></th>
                    <th><?= __('Status') ?></th>
                    <th><?= __('Impact') ?></th>
                    <th><?= __('Category') ?></th>
                    <th><?= __('Realizations') ?></th>
                    <th><?= __('Date') ?></th>
                    <?php
                    if ($admin || $supervisor || $senior_developer) { ?> 
                        <th class="actions"><?= __('Actions') ?></th>
                    <?php } ?>
                </tr>
                
            <?php foreach ($risks as $risk) : ?>
                <tr>
                    <td colspan="2"><?= h($risk->description) ?></td>
                    <td class="shortened shortenable"><?= h($risk->cause) ?></td>
                    <td class="shortened shortenable"><?= h($risk->mitigation) ?></td>
                    <td><?= h($risk->severity) ?></td>
                    <td><?= h($risk->probability) ?></td>
                    <td><?= h($risk->status) ?></td>
                    <td><?= h($risk->impact) ?></td>
                    <td><?= h($risk->category) ?></td>
                    <td><?= h($risk->realizations) ?></td>
                    <?php
                    // if weeklyrisk does not have date value, display weeklyreport's created_on value
                    if ($risk->date != null) { ?>                    
                        <td><?= h($risk->date->format('d.m.Y')) ?></td>
                    <?php } else { ?>
                        <td><?= h($weeklyreport->created_on->format('d.m.Y')) ?></td>
                    <?php } ?>  
                    <?php
                    // admins and supervisors can edit weeklyrisks
                    if ($admin || $supervisor || $senior_developer) { ?>     
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['controller' => 'Weeklyrisks', 'action' => 'edit', $risk->id]) ?>
                        </td> 
                    <?php } ?>
                </tr>   
                
            <?php endforeach; ?>
                
        </table>

        <?php endif; ?>
        
        <h4><?= __('Comments') ?></h4>
        <?php
            // query for comments
            $query = Cake\ORM\TableRegistry::get('Comments')
                        ->find()
                        ->select()
                        ->where(['weeklyreport_id =' => $weeklyreport['id']])
                        ->toArray();
            
        if (empty($query)) {
            echo "<p>No comments yet, be the first one!</p>";
        } else {
            // loop every query row
            for ($i=0; $i<sizeof($query); $i++) {
                // display info about user and time of the comment
                // data into variables
                $userquery = Cake\ORM\TableRegistry::get('Users')
                            ->find()
                            ->select(['first_name', 'last_name'])
                            ->where(['id =' => $query[$i]->user_id])
                            ->toArray();
                $fullname = $userquery[0]->first_name ." ". $userquery[0]->last_name;
                echo "<div class='messagebox'>";
                echo "<span class='msginfo'>" . $fullname . " left this comment on " . $query[$i]->date_created->format('d.m.Y, H:i') . "</span><br />";
                    
                                        
                                    echo "<div class='msg-content'><span>" . $query[$i]->content . "</span></div>";

                // display edit and delete options to owner and admin/SV
                if ($query[$i]->user_id == $this->request->getSession()->read('Auth.User.id') || ($admin || $supervisor)) {
                    echo "<div class='msgaction' data-edit-url='" . $this->Url->build(['controller' => 'Comments', 'action' => 'edit', $query[$i]->id]) . "' data-csrf-token='" . $this->request->getAttribute('csrfToken') . "'>";
                                            echo $this->Html->link(__('edit'), '#', ['class' => 'edit']);
                                            echo $this->Html->link(__('save'), '#', ['class' => 'save']);
                                            echo $this->Html->link(__('cancel'), '#', ['class' => 'cancel']);
                    echo $this->Html->link(__('delete'), ['controller' => 'Comments', 'action' => 'delete', $query[$i]->id], ['class' => 'delete']);
                    echo "</div>";
                }
                echo "</div>";
            }
        }
        ?>
        <?php
            // current time
            $datetime = date_create()->format('Y-m-d H:i:s');
            
            echo $this->Form->create(null, array('url'=>array('controller'=>'Comments', 'action'=>'add')));
        ?>
        <fieldset>
            <legend><?= __('New comment') ?></legend>
            <?= $this->Form->textarea('content') ?>
            <?= $this->Form->hidden('user_id', array('type' => 'numeric', 'value' => $this->request->getSession()->read('Auth.User.id') )) ?>
            <?= $this->Form->hidden('weeklyreport_id', array('type' => 'numeric', 'value' => $weeklyreport->id )) ?>
            <?php echo $this->Form->button('Submit comment', ['name' => 'submit', 'value' => 'submit']); ?>
        </fieldset>
        <?= $this->Form->end() ?>
    </div>
</div>
