<script src="/js/highcharts.js"></script>
<script src="/js/exporting.js"></script>
<script src="/js/chartview.js"></script>
<div class="members view large-8 medium-16 columns content float: left">
    <?= $role = "";
    if ($member->user->role == 'inactive') {
        $role = "(inactive)";
    }?>
    <h3>
        <?= h($member->user->first_name . " ". $member->user->last_name . " ". $role) ?>
    </h3>
    <?php
        $session = $this->request->getSession();
        // Initialize variable for total working hours
        $sum = 0;
        // Edit link not visible to devs or senior developers
        $admin = $session->read('is_admin');
        $supervisor = ( $session->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
    if ($admin || $supervisor) { ?>
            <div id="navbutton"><?= $this->Html->link(__('Edit Member'), ['action' => 'edit', $member->id]) ?> </div>
    <?php }
        // if member has workinghours and member's role is dev or senior developer
    if (($member->project_role == 'developer') || ($member->project_role == 'senior_developer')) {
        if (!empty($member->workinghours)) {?>
            <div id="navbutton"><?= $this->Html->link(__('Member\'s logged tasks'), ['controller' => 'Workinghours', 'action' => 'tasks', $member->id]) ?> </div>
        <?php }
    } ?>

    <div class="member-table">
        <div class="member-cell">
            <table class="vertical-table">
                <tr>
                    <th><?= __('Project Role') ?></th>
                    <td>
                        <?php
                            $member_role = array('client' => 'customer', 'developer' => 'developer', 'senior_developer' => 'senior developer', 'supervisor' => 'coach');
                                echo $member_role[$member->project_role];
                        ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Starting Date') ?></th>
                    <td><?php
                    if ($member->starting_date != null) {
                        echo h($member->starting_date->format('d.m.Y'));
                    }
                    ?></td>
                </tr>
                <tr>
                    <th><?= __('Ending Date') ?></th>
                    <td><?php
                    if ($member->ending_date != null) {
                        echo h($member->ending_date->format('d.m.Y'));
                    }
                    ?></td>
                </tr>
                <?php if (($member->project_role == 'developer') || ($member->project_role == 'senior_developer')) { ?>
                    <tr>
                        <th><?= __('Target hours') ?></th>
                        <td><?php
                        if ($member->target_hours != null) {
                            echo h($member->target_hours);
                        } else {
                            echo h('130');
                        }
                        ?></td>
                    </tr>
                <?php } ?>
                <?php if ($member->workinghours) { ?>
                    <tr>
                        <th><?= __('Last seen') ?></th>
                        <td><?php
                            // Get the date of member's latest working hour
                            $temp = $member->workinghours;
                            usort($temp, function ($a, $b) {
                                return $a['date'] <= $b['date'];
                            });
                            $lastSeen = $temp[0]->date->format('d.m.Y') ?? '';

                            echo h($lastSeen);
                            ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <?php
                        // Removed link from the email address
                    ?>
                    <th><?= __('Email') ?></th>
                    <td><?= $member->user->email ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="related">
        <?php if (!empty($member->workinghours)) : ?>
        <h4><?= __('Working hours') ?></h4>
            <table cellpadding="0" cellspacing="0">

                <tr>
                    <th><?= __('Worktype') ?></th>
                    <th><?= __('Hours') ?></th>
                </tr>

                <?php
                $query = $member->workinghours;
                $memberID = $member->id;
               
                foreach ($query as $temp) {
                    $hours[] = $temp->duration;
                    $sum = array_sum($hours);
                }
                // Fill array with zeros to avoid a bug if there are no workinghours of some work type
                $sums = array();
                $sums = array_fill(1, 9, 0);
                $id = 0;
                foreach ($query as $temp) {
                    $hour = 0;
                    if ($temp->worktype_id === 1) {
                        $hour = $temp->duration;
                        if (!(isset($sums[1]))) {
                            $sums[1] = $hour;
                        } else {
                            $sums[1] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 2) {
                        $hour = $temp->duration;
                        if (!(isset($sums[2]))) {
                            $sums[2] = $hour;
                        } else {
                            $sums[2] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 3) {
                        $hour = $temp->duration;
                        if (!(isset($sums[3]))) {
                            $sums[3] = $hour;
                        } else {
                            $sums[3] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 4) {
                        $hour = $temp->duration;
                        if (!(isset($sums[4]))) {
                            $sums[4] = $hour;
                        } else {
                            $sums[4] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 5) {
                        $hour = $temp->duration;
                        if (!(isset($sums[5]))) {
                            $sums[5] = $hour;
                        } else {
                            $sums[5] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 6) {
                        $hour = $temp->duration;
                        if (!(isset($sums[6]))) {
                            $sums[6] = $hour;
                        } else {
                            $sums[6] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 7) {
                        $hour = $temp->duration;
                        if (!(isset($sums[7]))) {
                            $sums[7] = $hour;
                        } else {
                            $sums[7] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 8) {
                        $hour = $temp->duration;
                        if (!(isset($sums[8]))) {
                            $sums[8] = $hour;
                        } else {
                            $sums[8] += $hour;
                        }
                    }
                    if ($temp->worktype_id === 9) {
                        $hour = $temp->duration;
                        if (!(isset($sums[9]))) {
                            $sums[9] = $hour;
                        } else {
                            $sums[9] += $hour;
                        }
                    }
                }
                // Get the names for worktypes
                $queryForTypes = Cake\ORM\TableRegistry::get('Worktypes')
                    ->find()
                    ->toArray();
                ?>

                <?php
                foreach ($queryForTypes as $type) : ?>
                <tr>
                    <td><?= h($type->description) ?></td>                
                    <td><?= h($sums[$type->id]) ?></td>

                <?php endforeach; ?>   
                </tr>
                <tr style="border-top: 2px solid black;">
                    <td><b><?= __('Total') ?></b></td> 
                    <td><b><?= h($sum) ?></b></td>
                </tr>    
            </table>
        <?php endif; ?>          
    </div>  

    <!-- Only display chart if member is developer or senior developer and if member has working hours -->
    <?php if ((($member->project_role == 'developer') || ($member->project_role == 'senior_developer')) && $sum > 0) { ?>
    <div class="chart">
        <div id="predictiveMemberChartWrapperJS">
        </div>
    </div> 
    <?php } ?>
</div>
<!-- Set formatting for the charts on page -->
<script>
    setPageChartOptions();    
</script>

<!-- Create the Working hours prediction chart with Highcharts JS -->
<script>
    var predictiveMemberData=<?php echo json_encode($session->read('predictiveMemberData'));?>;
    var seriesArray = [];
    for (let i = 0; i < predictiveMemberData.length; i++) {
        seriesArray.push({
            name: predictiveMemberData[i]['name'],
            data: predictiveMemberData[i]['hours'],
            marker: predictiveMemberData[i]['marker']
        })
    }
    createPredictiveMemberChart(predictiveMemberData, seriesArray);
</script>
