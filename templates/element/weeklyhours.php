<?php
$key = isset($key) ? $key : '<%= key %>';
?>
<tr>
    <td>
        <?php echo $this->Form->control("weeklyhours.{$key}.member_id"); ?>
    </td>
    <td>
        <?php echo $this->Form->control("weeklyhours.{$key}.duration"); ?>
    </td>  
    <td class="actions">
        <a href="#" class="remove">Remove Weeklyhours</a>
    </td>
</tr>
