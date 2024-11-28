<!-- This is the second page in the weeklyreport form.
     $current_metrics is what was previously placed in the form if the user visits this page a second time
-->
<?= $this->Html->script('trello.js') ?>

<!-- 
    If the lower navigation bar needed, links go here
    <ul class="side-nav">
    </ul>
-->

<div class="metrics form large-6 medium-12 columns content float: left">
    <?= $this->Form->create($metric) ?>
    <fieldset>
        <legend><?= __('Add Metrics, Page 2/4') ?></legend>
        <?php
            $current_metrics = $this->request->getSession()->read('current_metrics');
            
        ?>            
        <div style="display: flex; justify-content: flex-start;">
        <?php
            $metrics0 = $current_metrics[0]['value'] ?? null;
            $metrics1 = $current_metrics[1]['value'] ?? null;
            echo $this->Form->control(
                'phase',
                array('value' => $metrics0, 'label' => $metricNames[1],'type' => 'number', 'min' => 0, 'required' => true)
            );
            
            echo $this->Form->control(
                'totalPhases',
                array('value' => $metrics1, 'label' => $metricNames[2],'type' => 'number', 'min' => 0, 'required' => true)
            );

            echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'This can be for example number of sprints.', 'class' => 'infoicon', 'style' => 'align-self: center;']);
            ?>
        </div>
            <div class="boxed">
                <p>
                    <?php echo "Current state of the requirements list"; ?>
                </p>
                <?php if ($trello != null) : ?>
                <div id="trello-requirements"  data-board-id="<?= $trello->board_id ?>" data-app-key="<?= $trello->app_key ?>" data-token="<?= $trello->token ?>">
                    <?php foreach ($trello->trellolinks as $link) : ?>
                    <div class="trello-link" data-list-id="<?= $link->list_id ?>" data-req="<?= $link->requirement_type ?>"></div>
                    <?php endforeach; ?>
                </div>
                
                    <?php
                endif;
                ?>            
                <div style="display: flex; justify-content: flex-start;">
                <?php
                    $metrics2 = $current_metrics[2]['value'] ?? null;
                    $metrics3 = $current_metrics[3]['value'] ?? null;
                    $metrics4 = $current_metrics[4]['value'] ?? null;
                    $metrics5 = $current_metrics[5]['value'] ?? null;
                    echo $this->Form->control(
                        'reqNew',
                        array('value' => $metrics2, 'label' => $metricNames[3],'type' => 'number', 'min' => 0, 'required' => true)
                    );
                    echo $this->Form->control(
                        'reqInProgress',
                        array('value' => $metrics3, 'label' => $metricNames[4],'type' => 'number', 'min' => 0, 'required' => true)
                    );
                    echo $this->Form->control(
                        'reqClosed',
                        array('value' => $metrics4, 'label' => $metricNames[5],'type' => 'number', 'min' => 0, 'required' => true)
                    );
                    echo $this->Form->control(
                        'reqRejected',
                        array('value' => $metrics5, 'label' => $metricNames[6],'type' => 'number', 'min' => 0, 'required' => true)
                    );

                    echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'Product backlog is a list of the upcoming features.', 'class' => 'infoicon', 'style' => 'align-self: center;']);
                    echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'Sprint backlog is a list of features in progress.', 'class' => 'infoicon', 'style' => 'align-self: center;']);
                    ?>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-start;">
            <?php
                $metrics6 = $current_metrics[6]['value'] ?? null;
            if ($commitCount != null) {
                echo $this->Form->control(
                    'commits',
                    array('value' => $commitCount, 'label' => $metricNames[7],'type' => 'number', 'min' => 0, 'required' => true)
                );
            } else {
                echo $this->Form->control(
                    'commits',
                    array('value' => $metrics6, 'label' => $metricNames[7],'type' => 'number', 'min' => 0, 'required' => true)
                );
            }
                
                echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'Number of commits in master branch on your GitHub etc', 'class' => 'infoicon', 'style' => 'align-self: center;']);
            ?>
            </div>
            <div style="display: flex; justify-content: flex-start;">
            <?php
                $metrics7 = $current_metrics[7]['value'] ?? null;
                $metrics8 = $current_metrics[8]['value'] ?? null;
                echo $this->Form->control(
                    'passedTestCases',
                    array('value' => $metrics7, 'label' => $metricNames[8],'type' => 'number', 'min' => 0, 'required' => true)
                );

                echo $this->Form->control(
                    'totalTestCases',
                    array('value' => $metrics8, 'label' => $metricNames[9],'type' => 'number', 'min' => 0, 'required' => true)
                );

                ?>
            </div>
            <div style="display: flex; justify-content: flex-start;">
            <?php
                $metrics9 = $current_metrics[9]['value'] ?? null;
                echo $this->Form->control(
                    'degreeReadiness',
                    array('value' => $metrics9, 'label' => $metricNames[10],'type' => 'number', 'min' => 1, 'max' => 100, 'required' => true)
                );
                echo $this->Html->image('../webroot/img/infoicon.png', ['alt' => 'infoicon', 'title' => 'Estimate your project\'s degree of readiness (0-100%).', 'class' => 'infoicon', 'style' => 'align-self: center;']);
                
                ?>
            </div>
            <div style="display: flex; justify-content: flex-start;">
            <?php
                $metrics9 = $current_metrics[9]['value'] ?? null;
                echo $this->Form->control('overallStatus', ['options' => array(1 => 'All OK', 2 => 'Minor issues', 3 => 'Serious problems'),
                'empty' => ' ', 'label' => $metricNames[11], 'required' => true]);
                ?>
            </div>
            <div class="report-nav">
                <?= $this->Form->button('Next page', ['name' => 'submit', 'value' => 'next']);?>
                    <?= $this->Html->link('Previous page', ['name' => 'submit', 'value'=>'previous', 'controller' => 'Weeklyreports', 'action' => 'add'], ['class' => 'link-button', 'style' => 'align-self: center;']); ?>
            </div>            
    </fieldset>
   
    <?= $this->Form->end() ?>
</div>
<style>
   .infoicon{float: right; margin-top: 31px; margin-left: 10px;}
</style>
