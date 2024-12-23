<?php $this->assign('title', 'Statistics');?>

<?php use Cake\I18n\Time; ?>
<?php if ($this->request->getSession()->read('is_admin') || $this->request->getSession()->read('is_supervisor')) { ?>
    <div class="statistics">
        <h3><?= __('Edit limits') ?></h3> 
        <?= $this->Form->create() ?>
        <div id="chart-limits">
        <?php
            echo $this->Form->control('weekmin', array('type' => 'number', 'value' => $this->request->getSession()->read('statistics_limits')['weekmin']));
            echo $this->Form->control('weekmax', array('type' => 'number', 'value' => $this->request->getSession()->read('statistics_limits')['weekmax']));
            echo $this->Form->control('year', array('type' => 'number', 'value' => $this->request->getSession()->read('statistics_limits')['year']));
        ?>
        </div>
        <button>Submit</button>
        <?= $this->Form->end() ?>
    </div>
<?php } ?>

<div class="projects view large-9 medium-18 columns content float: left">
    <h3><?= h('Statistics') ?></h3>
    
    <?php if ($this->request->getSession()->read('is_admin') || $this->request->getSession()->read('is_supervisor')) { ?>
        <h4><?= h('Weekly reports') ?></h4>
        <table class="stylized-table stat-table">
            <tbody>
                <tr class="header">
            <!-- empty cell -->
                    <td class="primary-cell"></td>

                    <?php
                    $min = $this->request->getSession()->read('statistics_limits')['weekmin'];
                    $max = $this->request->getSession()->read('statistics_limits')['weekmax'];
                    $year = $this->request->getSession()->read('statistics_limits')['year'];
                    
                    // correction for nonsensical values
                    if ($min < 1) {
                        $min = 1;
                    }
                    if ($min > 53) {
                        $min = 53;
                    }
                    if ($max < 1) {
                        $max = 1;
                    }
                    if ($max > 53) {
                        $max = 53;
                    }
                    if ($max < $min) {
                        $temp = $max;
                        $max = $min;
                        $min = $temp;
                    }
                    
            /* REMOVED after deemed too restricting. If you want to implement this again,
                    * find and change this piece of code also in ProjectsController.
            // for clear displaying purposes, amount of columns is limited to 11 (name + 10 weeks)
            if ( ($max - $min) > 9 ) {
                $max = $min + 9;
            } */

                    for ($x = $min; $x <= $max; $x++) {
                        echo "<td>$x</td>";
                    }
                    ?>
                </tr>
                
                <?php foreach ($projects as $project) : ?>
                <tr class="trow">
                    <td class="primary-cell"><?= $this->Html->link(__($project['project_name']), ['action' => 'view', $project['id']]) ?></td>
                        <?php
                        $admin = $this->request->getSession()->read('is_admin');
                        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;

                        // query iterator, resets after finishing one row
                        $i = 0;

                        foreach ($project['reports'] as $report) :
                            ?>
                        
                            <?php
                        // missing ones print normally
                            if ($report == '-') { ?>
                        <td>
                                <?= h($report) ?>
                        </td>
                                <?php
                            } else {
                                // fetching the ID for current weeklyreport's view-page
                                $query = Cake\ORM\TableRegistry::get('Weeklyreports')
                                    ->find()
                                    ->select(['id'])
                                    ->where(['project_id =' => $project['id'],
                                            'week >=' => $min, 'year >=' => $year])
                                    ->toArray();
                                // transforming returned query item to integer
                                $reportId = $query[$i++]->id;

                                $metricsTable = Cake\ORM\TableRegistry::get('Metrics');
                                $queryM = $metricsTable
                                ->find()
                                ->select(['value'])
                                ->where(['weeklyreport_id' => $reportId, 'metrictype_id =' => 11])
                                ->toArray();

                            
                                $status = 1;
                                if (sizeof($queryM) == 1) {
                                    $status = $queryM[0]['value'];
                                }

                                ?>

                            <td
                                <?php if ($status == 3) {
                                    echo(' style="background-color:#ff5757"');
                                } else if ($status == 2) {
                                    echo(' style="background-color:#ffef85"');
                                } else {
                                    echo(' style="background-color:#ccffd7"');
                                } ?> >

                                <?php
                                // X's have normal link color so they echo normally
                                if ($report == 'X') {
                                    echo $this->Html->link(__($report.' (view)'), [
                                    'controller' => 'Weeklyreports',
                                    'action' => 'view',
                                    $reportId ]);
                                        // unread weeklyreports have some mark indicating it
                                        $userid = $this->request->getSession()->read('Auth.User.id');
                                        $newreps = Cake\ORM\TableRegistry::get('Newreports')->find()
                                            ->select()
                                            ->where(['user_id =' => $userid, 'weeklyreport_id =' => $reportId])
                                            ->toArray();
                                    if (sizeof($newreps) > 0) {
                                        echo "<div style='font-style: italic;'>unread</div>";
                                    }
                                } else {
                                    echo $this->Html->link(__($report.' (view)'), [
                                    'controller' => 'Weeklyreports',
                                    'action' => 'view',
                                    $reportId ], ['style'=>'color: black;']);
                                }
                            } ?>
                        </td>
      
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h4>Metrics</h4>
        <table class="stylized-table">
        <tbody>
            <tr class="header">
                <td style="width:220px;">Project name</td>
                <td>Commits</td>
                <td>Test cases (passed / total)</td>
                <td>Backlog (product / sprint)</td>
                <td>Done</td>
                <td>Risks (high / total)</td>
                <td>CPI / SPI</td>              
            </tr>
            <?php foreach ($projects as $project) : ?>
                <tr class="trow">
                    <td><?= $this->Html->link(__($project['project_name']), ['action' => 'view', $project['id']]) ?></td>
                    <td <?= $project['statusColors']['commits'] ?> ><?= h($project['metrics'][6]['value']) ?></td>
                    <td <?= $project['statusColors']['testCases'] ?> ><?= h($project['metrics'][7]['value'] . ' / ' . $project['metrics'][8]['value']) ?></td>
                    <td <?= $project['statusColors']['backlog'] ?> ><?= h($project['metrics'][2]['value'] . ' / ' . $project['metrics'][3]['value']) ?></td>
                    <td <?= $project['statusColors']['done'] ?> ><?= h($project['metrics'][4]['value']) ?></td>
                    <td <?= $project['statusColors']['risks'] ?> ><?= h($project['risks'][0] . ' / ' . $project['risks'][1]) ?></td>
                    <td <?= $project['statusColors']['CPI/SPI'] ?> ><?php if ($project['earnedValueData'] != null) {
                        echo(round($project['earnedValueData'][6]['CPI'], 2) . ' / ' . round($project['earnedValueData'][6]['SPI'], 2));
                        } ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody> 
    </table>
    <?php }?>

    <h4>Members and hours</h4>
    <table class="stylized-table">        
        <tbody>
            <tr class="header">
                <th>Project name</th>
                <th>Number of members</th>
                <th>Total number of working hours</th>
            <?php if ($this->request->getSession()->read('is_admin') || $this->request->getSession()->read('is_supervisor')) { ?>
                <th>Minimum working hours of an active member</th>
                <th>Earliest last seen date of an active member</th>                
            <?php }?>
            </tr>
            <?php foreach ($projects as $project) : ?>
                <tr class="trow">
                    <td><?= $this->Html->link(__($project['project_name']), ['action' => 'view', $project['id']]) ?></td>
                    <td><?= h($project['userMembersCount']) ?></td>
                    <td><?= h($project['totalHours']) ?></td>
                <?php if ($this->request->getSession()->read('is_admin') || $this->request->getSession()->read('is_supervisor')) { ?>
                    <td <?= $project['statusColors']['minimumHours'] ?> ><?= h($project['minimumHours']) ?></td>
                    <td <?= $project['statusColors']['lastSeen'] ?> ><?php if ($project['earliestLastSeenDate'] != null) {
                        echo date("d.m.Y", strtotime($project['earliestLastSeenDate']));
                        } ?></td>
                <?php }?>
                </tr>
            <?php endforeach; ?>
        </tbody> 
    </table>
</div>
