<table class="widefat" cellspacing="0">
	<thead>
		<tr>
			<th class="overview-id">ID</th>
			<th width="60%">Name</th>
			<th>Description</th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Description</th>
		</tr>
	</tfoot>

	<tbody>
    	<?php
        foreach ($projects as $project) {
            echo '<tr height="50" id="project-' . $project->id . '" valign="middle">';
            foreach ($columns as $column_name => $column_display_name) {
                switch ($column_name) {
                    case 'project_id':
                        echo '<td class="project-id" valign="middle"><strong>' .  $project->id . '</strong></td>';    
                    	break;
                    case 'description':
                        echo '<td class="project-id" valign="middle">' .  $project->description . '</td>';    
                    	break;
                    case 'project':
                        echo '<td valign="middle"><strong>' .  $project->title . '</strong><br/>';
                        if(isset($project->project_home_url)) {
							echo 'Home url: <a href="' .  $project->project_home_url . '">' .  $project->project_home_url . '</a><br/>';
                        }
                        $all_tasks = $this->model->getTasks(13, $project->id);
                        echo 'Opened issues:' .  count($this->model->getTasks(14, $project->id)) . '; Total issues:' .  count($all_tasks) . '<br/>';
                        if(count($all_tasks) > 0) {
							echo 'Next release date:' .  $all_tasks[0]->end . '<br/><br/>';
                        }
                        echo $this->model->buildProjectStatus($project->id);
                        echo '</td>';   
                    	break;
                }
            }
        }
        ?>
        </tr>
	</tbody>
</table>
