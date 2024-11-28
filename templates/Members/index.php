<script src="/js/highcharts.js"></script>
<script src="/js/exporting.js"></script>
<script src="/js/chartview.js"></script>
<?php use Cake\I18n\Time; ?>

<div class="members index large-9 medium-18 columns content float: left">
    <h3><?= __('Members') ?></h3>
    <?php
        $session = $this->request->getSession();
            $admin = $session->read('is_admin');
            $supervisor = ( $session->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            // FIX: senior developers can also add new members
            $senior_developer = ( $session->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
            // Get if the user is inactive
            $inactive = ( $session->read('Auth.User.role') === 'inactive' ) ? 1 : 0;

            
    if ($admin || $supervisor || $senior_developer) {
        ?>            
            <!-- This button has been changed to link because of the accessibility.-->
            <div id="navbutton"><?= $this->Html->link(__('+ New Member'), ['action' => 'add']) ?></div>
    <?php } ?>
        
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="image-cell"></th>
                <th colspan="2"><?= __('Name') ?></th>
                <th><?= $this->Paginator->sort('project_role') ?></th>
                <th><?= __('Working hours') ?></th>
                <th><?= __('Last seen') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; ?>
            <?php
                // Sort members, so that inactive members are last.
                $members = $members->sortBy(function ($record) {
                    $role = $record->user->role;
                    if (strcmp($role, "inactive")) {
                        return 1;
                    } else {
                        return 0;
                    }
                }) ?>
            <?php foreach ($members as $member) : ?>
                <?php
                // Get the sum of workinghours for a member who has working hours
                $lastSeen = null;
                if (($member->project_role == 'developer' || $member->project_role == 'senior_developer') && !empty($member->workinghours)) {
                    $query = $member->workinghours;
                    $hours = array();
                    foreach ($query as $key) {
                        $hours[] = $key->duration;
                    }
                    $sum = array_sum($hours);

                    // Get the date of member's latest working hour
                    $temp = $member->workinghours;
                    usort($temp, function ($a, $b) {
                        return $a['date'] <= $b['date'];
                    });
                    $lastSeen = $temp[0]->date;
                } else {
                    $sum = 0;
                }
                $total += $sum;
                $target = $member->target_hours;
                if ($target == null) {
                    $target = 0;
                }
                ?>
            <?php if ($inactive) { 
                // If the user is inactive, only show their own working hours
                if ($member->user_id !== $session->read('Auth.User.id')) {
                    continue;
                }
            } ?>
            <tr>
                <td class="image-cell">
                    <?= $this->Custom->profileImage($member->user_id); ?>
                </td>
                <?php $inactiveUser = $member->user->role == 'inactive' ?>
                <td colspan="2"><?php
                if ($member->has('user')) {
                    echo $this->Html->link(
                        $member->user->first_name . " " . $member->user->last_name . " " . ($inactiveUser ? "(inactive)" : ""),
                        ['controller' => 'Members', 'action' => 'view', $member->id],
                        $inactiveUser ? ['class' => 'inactive'] : []
                    );
                } else {
                    echo '';
                } ?></td>
                <td>
                    <?php
                        $member_role = array('client' => 'customer', 'developer' => 'developer', 'senior_developer' => 'senior developer', 'supervisor' => 'coach');
                        echo $member_role[$member->project_role];
                    ?>
                </td>


                <td>
                <?php if ($member->project_role == 'developer' || $member->project_role == 'senior_developer') {
                    echo ($sum . ' / ' . $target);
                } ?></td>
                <td><?php
                if ($member->project_role == 'developer' || $member->project_role == 'senior_developer') {
                    if ($lastSeen != null) {
                        echo ($lastSeen->format('d.m.Y'));
                    } else {
                        echo ('Never');
                    }
                }
                ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $member->id]) ?>
                    <?php
                        $admin = $session->read('is_admin');
                        $supervisor = ( $session->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
                        $senior_developer = ( $session->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
                        $current_user_id = $session->read('Auth.User.id');
                        // admin and supervisor can edit all members. Senior developer can edit only developers and themselves.
                        if ($admin || $supervisor || ($senior_developer && $member->project_role == 'developer') || ($senior_developer && $member->user_id == $current_user_id)) {
                            echo $this->Html->link(__('Edit'), ['action' => 'edit', $member->id]);
                        }
                        // admin, supervisor can delete members
                        if ($admin || $supervisor) {
                            echo $this->Form->postLink(__('Delete'), ['action' => 'delete', $member->id], ['confirm' => __('Are you sure you want to delete # {0}?', $member->id)]);
                        }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php
            $totalTarget = 0;
            foreach ($members as $member) :
                if (($member->project_role == 'developer' || $member->project_role == 'senior_developer') && $member->target_hours != null) {
                    $totalTarget += $member->target_hours;
                }
            endforeach;
            ?>
            
            <?php if (!empty($member->project_id)) { ?>
            <tr style="border-top: 2px solid black;">
                <td></td>
                <td colspan="2"><b><?= __('Total') ?></b></td>
                <td></td>
                <td><b><?= h($total . ' / ' . $totalTarget) ?></b></td>
                <td></td>
                <td></td>
            </tr> 
            <?php } ?>
        </tbody>
    </table>
    <!-- Only display chart if project has working hours -->
    <?php if ($total > 0 && !$inactive) { ?>
    <div class="chart">
        <div id="predictiveProjectChartWrapperJS">
        </div>
    </div>
    <?php } ?>
    <?php if (!$inactive) { ?>
        <br>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Worktype') ?></th>
                <th><?= __('Hours') ?></th>
                <th><?= __('Percentage') ?></th>
            </tr>
            
            <?php
            $queryForTypes = Cake\ORM\TableRegistry::get('Worktypes')
                ->find()
                ->toArray();
            
            foreach ($queryForTypes as $type) : ?>
                <tr>
                    <td><?= h($type->description) ?></td>                
                    <td><?= h($hoursByTypeData[$type->id]) ?></td>
                    <td>
                    <?php
                    if (!$hoursByTypeData[$type->id]) {
                        echo(0);
                    } else {
                        $percent = round(($hoursByTypeData[$type->id]/$total * 100), 0, PHP_ROUND_HALF_UP);
                        echo($percent);
                    }
                    ?>
                    </td>
                </tr>
            <?php endforeach; ?>   
            
            <tr style="border-top: 2px solid black;">
                <td><b><?= __('Total') ?></b></td> 
                <td><b><?= h($total) ?></b></td>
                <td><b><?= h(100) ?></b></td>
            </tr>    
        <?php } ?>
    </table>        
    
    <!-- Anonymize members button doesn't work correctly (doesn't really anonymize members).
        The functionality of this button changes members' user_id (members table in database) so that
        the user_id refers to another user.
    <?php  // if ($admin) { ?>
    <a href="<?php // $this->Url->build(['controller' => 'Members', 'action' => 'anonymizeAll']) ?>" 
        onclick="return confirm('Are you sure you want to anonymize all members of the project (this cannot be reversed)?');">Anonymize members</a>
    <?php // } ?>
    -->
</div>

<!-- Set formatting for the charts on page -->
<script>
    setPageChartOptions();    
</script>

<!-- Create the Working hours prediction chart with Highcharts JS -->
<script>
    var predictiveProjectData=<?php echo json_encode($session->read('predictiveProjectData'));?>;
    var seriesArray = [];
    for (let i = 0; i < predictiveProjectData.length; i++) {
        seriesArray.push({
            name: predictiveProjectData[i]['name'],
            data: predictiveProjectData[i]['hours'],
            marker: predictiveProjectData[i]['marker']
        })
    }
    createPredictiveProjectChart(predictiveProjectData, seriesArray);
</script>
