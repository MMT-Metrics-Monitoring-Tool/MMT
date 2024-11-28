
<div class="projects view large-7 medium-16 columns content float: left">
    <h3><?= h($project->project_name) ?></h3>
    <?php
        $admin = $this->request->getSession()->read('is_admin');
        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
        $senior_developer = ( $this->request->getSession()->read('selected_project_role') == 'senior_developer' ) ? 1 : 0;
                                
    if ($admin || $supervisor || $senior_developer) { ?>
            <!--This button has been changed to link because of the accessibility.-->
            <div id="navbutton"><?= $this->Html->link(__('Edit Project'), ['action' => 'edit', $project->id]) ?> </div>
    <?php }
    if ($admin) { ?>   
            <!--This button has been changed to link because of the accessibility.-->
            <div id="managing_button"><?= $this->Html->link(__('Metrics'), ['controller' => 'Metrics', 'action' => 'index']) ?> </div>
    <?php }
    ?>
    <p>
        <?= h($project->description) ?>
    </p>
    <table class="vertical-table">
        <tr>
            <th><?= __('Starting Date') ?></th>
            <td><?= h($project->created_on->format('d.m.Y')) ?></tr>
        </tr>
        <tr>
            <th><?= __('Updated On') ?></th>
            <td><?php
            if ($project->updated_on != null) {
                echo h($project->updated_on->format('d.m.Y'));
            }
            ?></tr>
        </tr>
        <tr>
            <th><?= __('Estimated Completion Date') ?></th>
            <td><?php
            if ($project->finished_date != null) {
                echo h($project->finished_date->format('d.m.Y'));
            }
            ?></tr>
        </tr>
        <tr>
            <th><?= __('Customer') ?></th>
            <td><?php
            if ($project->customer != null) {
                echo h($project->customer);
            }
            ?></tr>
        </tr>
    </table>
    <!-- only senior developers, supervisor and admin can see Slack and Trello links -->
    <?php if (in_array($this->request->getSession()->read('selected_project_role'), ['senior_developer','admin','supervisor'])) : ?>
        <h4><?= h("Project's connection settings:") ?></h3>
        <table class="vertical-table">
            <tr>
                <th><?= $this->Html->link(__('Slack'), ['controller' => 'Slack', 'action' => 'index']) ?></th>
            </tr>
            <tr>
                <th><?= $this->Html->link(__('Trello'), ['controller' => 'Trello', 'action' => 'index']) ?></th>
            </tr>
            <tr>
                <th><?= $this->Html->link(__('GitHub'), ['controller' => 'Git', 'action' => 'index']) ?></th>
            </tr>
        </table>
    <?php endif; ?> <!-- end if senior_developer/supervisor/admin -->
</div>