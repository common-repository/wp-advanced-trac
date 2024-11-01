<?php
$project_columns = array('cb' => '<input type="checkbox" />' , 'name' => __('Name'), 'start' => __('Start'), 'end' => __('End'), 'tasks' => __('Tasks'));
register_column_headers('project-manager', $project_columns);

?>

<form action="" method="GET">
<input type="hidden" value="wp-trac/projects" name="page" />
<div class="tablenav">
<div class="alignleft actions">

	<select name="action">
		<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
		<option value="delete"><?php _e('Delete'); ?></option>
		<option value="complete"><?php _e('Complete'); ?></option>
	</select> 
	<input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" onClick="return confirm('You are about to perform this action on the selected projects and all associated tasks.\n \'Cancel\' to stop, \'OK\' to continue.')" class="button-secondary action" />

    <input type="button" value="<?php esc_attr_e('Add Project'); ?>"
    	name="newproject" id="newproject"
    	onclick="window.location.href='admin.php?page=wp-advanced-trac/projects&action=create'"
    	class="button-secondary action" />	

</div>

<div class="view-switch">
	<a href="<?php echo esc_url(add_query_arg('mode', 'list', remove_query_arg('action', $_SERVER['REQUEST_URI'])))?>">
		<img <?php if ('list' == $mode) echo 'class="current"'; ?> id="view-switch-list" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('List View')?>" alt="<?php _e('List View')?>" />
	</a> 
	<a href="<?php echo esc_url(add_query_arg('mode', 'excerpt', remove_query_arg('action', $_SERVER['REQUEST_URI'])))?>">
		<img <?php if ('excerpt' == $mode) echo 'class="current"'; ?> id="view-switch-excerpt" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('Excerpt View')?>" alt="<?php _e('Excerpt View')?>" />
	</a>
</div>

<div class="clear"></div>


<table class="widefat post fixed" cellspacing="0">
	<thead>
		<tr>
<?php
print_column_headers('project-manager');
?>
	</tr>
	</thead>

	<tfoot>
		<tr>
<?php
print_column_headers('project-manager', false);
?>
	</tr>
	</tfoot>

	<tbody>
	<?php
foreach ($projects as $project) {
    echo '<tr id="link-' . $project->id . '" valign="middle"' . $style . '>';
    foreach ($project_columns as $column_name => $column_display_name) {
        $class = "class=\"column-$column_name\"";
        switch ($column_name) {
            case 'cb':
                echo '<th scope="row" class="check-column"><input type="checkbox" name="check[]" value="' . esc_attr($project->id) . '" /></th>';
                break;
            case 'name':
                $edit_link = admin_url('admin.php?page=wp-advanced-trac/projects&amp;action=edit&amp;id=') . $project->id;
                $delete_link = admin_url('admin.php?page=wp-advanced-trac/projects&amp;action=delete&amp;id=') . $project->id;
                $complete_link = admin_url('admin.php?page=wp-advanced-trac/projects&amp;action=complete&amp;id=') . $project->id;
                echo "<td width='200'><strong><a class='row-title' href='$edit_link' title='" . esc_attr(sprintf(__('Edit &#8220;%s&#8221;'), $project->title)) . "'>$project->title</a></strong><br />";
                if ($_GET['mode'] == "excerpt")
                    echo $project->description;
                $actions = array();
                $actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit') . '</a>';
                $actions['complete'] = "<a class='submitcomplete' href='" . $complete_link . "'>" . __('Complete') . "</a>";
                $actions['delete'] = "<a class='submitdelete' href='" . $delete_link . "' onclick=\"if ( confirm('" . esc_js(sprintf(__("You are about to delete the project '%s' and all tasks associated with it.\n  'Cancel' to stop, 'OK' to delete."), $project->title)) . "') ) { return true;}return false;\">" . __('Delete') . "</a>"; 
                $action_count = count($actions);
                $i = 0;
                echo '<div class="row-actions">';
                foreach ($actions as $action => $linkaction) {
                    ++ $i;
                    ($i == $action_count) ? $sep = '' : $sep = ' | ';
                    echo "<span class='$action'>$linkaction$sep</span>";
                }
                echo '</div>';
                
                echo '</td>';
                break;
            case 'client':
                echo "<td $attributes>" .  $project->client . "</td>";
                break;
            case 'start':
                echo "<td width='100'>" .  date("Y/m/d", strtotime($project->start)) . "</td>";
                break;
            case 'end':
                echo "<td width='100'>" .  date("Y/m/d", strtotime($project->end)) . "</td>";
                break;   
            case 'tasks':
            	$total = count($this->model->getTasks(6, $project->id));
            	$complete = count($this->model->getTasks(7, $project->id));   
            	$incomplete = $total - $complete; 
            	echo '<td>';
            	
            	if ($_GET['mode'] == "excerpt") {
            		echo "Total: " . $total . "<br />";
                    echo "Complete: " . $complete . "<br />";
                    echo "Incomplete: " . $incomplete;
            	} else {
            		echo  $total . "<br />";
            	}
            	echo '</td>';

            	
            	break;
            default:
                ?>
					<td><?php do_action('manage_link_custom_column', $column_name, $project->id); ?></td>
					<?php
                break;
        }
    }
}
?>
        </tr>
	</tbody>
</table>

    <div class="tablenav">
        <div class="alignleft actions">
            <select name="action2">
            	<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
            	<option value="delete"><?php _e('Delete'); ?></option>
            	<option value="complete"><?php _e('Complete'); ?></option>
            </select> 
           
            <input type="submit" class="button-secondary action" value="<?php esc_attr_e('Apply'); ?>" name="doaction2" onClick="return confirm('You are about to perform this action on the selected projects and all associated tasks.\n \'Cancel\' to stop, \'OK\' to continue.')" />

            
            <input type="button" value="<?php esc_attr_e('Add Project'); ?>"
            	name="newproject" id="newproject"
            	onclick="window.location.href='admin.php?page=wp-advanced-trac/projects&action=create'"
            	class="button-secondary action" />
        </div>
        <br class="clear" />
    </div>  
    <div id="ajax-response"></div>
</div>
</form>
