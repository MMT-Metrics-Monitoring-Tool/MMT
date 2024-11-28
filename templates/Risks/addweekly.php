
<!-- 
    If the lower navigation bar needed, links go here
    
    <ul class="side-nav">
        
    </ul>
-->

<!-- This is the third page in the weeklyreport form.
-->

<div class="metrics form large-6 medium-12 columns content float: left">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Add Risks, Page 3/4') ?></legend>
        <?php if (!empty($risks)) : ?>
        <table>
            <tr>
                <th>Risk</th>
                <th>Severity</th>
                <th>Probability</th>
                <th>Importance</th>
                <th>Status</th>
                <th>Category</th>
                <th>Impact</th>
                <th>Times realized this week</th>
            </tr>
            <?php foreach ($risks as $risk) : ?>
            <tr>
                <td><?= $risk->description ?></td>
                <td><?= $this->Form->control('', ['value' => $current_risks[$risk->id]['severity'], 'name' => 'severity-'.$risk->id, 'options' => $types]);  ?></td>
                <td><?= $this->Form->control('', ['value' => $current_risks[$risk->id]['probability'], 'name' => 'prob-'.$risk->id, 'options' => $types]);  ?></td>
                <td><?= $types[max(1, ($current_risks[$risk->id]['severity'] + $current_risks[$risk->id]['probability']) / 2)] ?></td>
                <td><?= $this->Form->control('', ['value' => $current_risks[$risk->id]['status'], 'name' => 'status-'.$risk->id, 'options' => $statusTypes]);  ?></td>
                <td><?= $this->Form->control('', ['value' => $current_risks[$risk->id]['category'], 'name' => 'category-'.$risk->id, 'options' => $categories]);  ?></td>
                <td><?= $this->Form->control('', ['value' => $current_risks[$risk->id]['impact'], 'name' => 'impact-'.$risk->id, 'options' => $impactTypes]);  ?></td>
                <td class="times-realized-number"><?= $this->Form->control('', ['value' => '0', 'label' => '', 'name' => 'real-'.$risk->id, 'type' => 'number', 'min' => '0', 'max' => '100']) ?></td>
            </tr>
            
 
        
            <?php endforeach; ?>
        </table>
        <?php else : ?>
        <p>This project has no registered risk. Please proceed to next page.</p>
        <?php endif; ?>

        
        
        <div class="report-nav">
        <?= $this->Html->link('Previous page', ['name' => 'submit', 'value'=>'previous', 'controller' => 'Metrics', 'action' => 'addmultiple'], ['class' => 'link-button']); ?>
        <?= $this->Form->button('Next page', ['name' => 'submit', 'value' => 'next']); ?>
        </div>
    </fieldset>
   
    <?= $this->Form->end() ?>
</div>


