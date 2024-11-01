<form action="" method="POST">
	<input type="hidden" value="wp-advanced-trac/tasks" name="page" />
		<div class="tablenav">
		
			<div class="alignleft actions">
            	<select name="action">
            		<option value="" selected="selected"><?php _e('Bulk Actions'); ?></option>
            		<option value="delete"><?php _e('Delete'); ?></option>
            		<option value="complete"><?php _e('Complete'); ?></option>
            	</select> 
            	
				<input type="submit" value="<?php esc_attr_e('Apply'); ?>" 
					name="doaction" id="apply" class="button-secondary action" />

				<input type="submit" value="<?php esc_attr_e('Add Task'); ?>" 
					name="action" id="addtask" class="button-secondary action" />
                	
    	        <select name="filter">
    	        	<option value="0">Filter Tasks</option>
                    <option value="1">Complete</option>
                    <option value="2">Incomplete</option>
                    <option value="3">Priority</option>
                    <option value="4">Start</option>
                    <option value="5">End</option>             		               		
                    <option value="0">Projects</option>
                    <?php foreach($projects as $project) {?>
                    <option value="6-<?php echo $project->id; ?>" class="wp-trac-indent"><?php echo $project->title; ?></option>
                    <?php }?>
                </select>
                
				<input type="submit" value="<?php esc_attr_e('Filter'); ?>" 
					name="action" id="filter" class="button-secondary action" />
			</div>

            <div class="view-switch">
        		<a href="<?php echo $list_mode ?>">
        			<img <?php if ('list' == $mode) echo 'class="current"'; ?> 
        				id="view-switch-list" src="../wp-includes/images/blank.gif" 
        				width="20" height="20" title="<?php _e('List View')?>" 
        				alt="<?php _e('List View')?>" />
        		</a> 
        		<a href="<?php echo $excerpt_mode ?>">
        			<img <?php if ('excerpt' == $mode) echo 'class="current"'; ?> 
        				id="view-switch-excerpt" src="../wp-includes/images/blank.gif" 
        				width="20" height="20" title="<?php _e('Excerpt View')?>" 
        				alt="<?php _e('Excerpt View')?>" />
        		</a>
        			

    			
        	</div>

        	<div class="clear"></div>


			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="check-column"><input type="checkbox" /></th>
						<th width="40%">Name</th>
						<th>User</th>
						<th>Start</th>
						<th>End</th>
						<th width="15%">Project</th>
						<th style="text-align:center;" width="5%">Priority</th>
						<th style="text-align:center;" width="7%">Complete</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class="check-column"><input type="checkbox" /></th>
						<th width="40%">Name</th>
						<th>User</th>
						<th>Start</th>
						<th>End</th>
						<th width="15%">Project</th>
						<th style="text-align:center;" width="5%">Priority</th>
						<th style="text-align:center;" width="7%">Complete</th>
					</tr>
				</tfoot>

	<tbody>
	
    <?php
    foreach ($tasks as $task) {
        $edit_task = admin_url('admin.php?page=wp-advanced-trac/tasks&amp;action=edit&amp;id=') . $task->id;
        $delete_task = admin_url('admin.php?page=wp-advanced-trac/tasks&amp;action=delete&amp;id=') . $task->id;
    	$project = $this->model->getProject($task->pid);
    	$edit_project = admin_url('admin.php?page=wp-advanced-trac/projects&amp;action=edit&amp;id=') . $project->id;
    	if($task->uid == 0) {
    		$user = "Anyone";
    	} else {
    		$user = $this->model->getUser($task->uid);
    		$user = $user->user_nicename;
    	}
    ?>
    <tr id="task-<?php echo $task->id; ?>" valign="middle">
    	<th scope="row" class="check-column"><input type="checkbox" name="check[]" value="<?php echo esc_attr($task->id); ?>" /></th>
    
        <td>
        	<strong><a class="row-title" href="<?php echo $edit_task; ?>"><?php echo $task->title; ?></a></strong><br />
        	<?php if ($_GET['mode'] == "excerpt") echo $task->description; ?>
            <div class="row-actions">
            	<a href="<?php echo $edit_task; ?>">Edit</a> |	
            	<a class="submitdelete" href="<?php echo $delete_task; ?>" 
            	onclick="if (
                				confirm("Hello World")
                        	 ) { return true;}return false;">Delete</a>
            </div>
        </td>
        <td><?php echo $user; ?></td>
        <td><?php echo date("Y/m/d", strtotime($task->start)); ?></td>
        <td><?php echo date("Y/m/d", strtotime($task->end)); ?></td>
        <td><a href="<?php echo $edit_project; ?>"><?php echo $project->title; ?></a></td>
        <td style="text-align:center;"><?php echo $task->priority; ?></td>    
        <td style="text-align:center;"><?php echo $task->complete; ?>%</td>         

    <?php 
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
           
            <input type="submit" value="<?php esc_attr_e('Apply'); ?>"
            	name="doaction2" class="button-secondary action" /> 
            	
			<input type="submit" value="<?php esc_attr_e('Add Task'); ?>" 
				name="action" id="addtask2" class="button-secondary action" />
        </div>
        <br class="clear" />
    </div>  
    <div id="ajax-response"></div>
</div>
</form>
