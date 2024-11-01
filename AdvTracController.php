<?php

class AdvTracController
{
    private $model = NULL;
    private $months = array("" => "", "01" => "January" , "02" => "February" , "03" => "March" , "04" => "April" , "05" => "May" , "06" => "June" , "07" => "July" , "08" => "August" , "09" => "September" , "10" => "October" , "11" => "November" , "12" => "December");


    public function AdvTracController ()
    {
        $this->model = new AdvTracModel();
    }


    public function overview ()
    {
        
        $columns = array('project_id' => __('ID') , 'project' => __('Project'), 'project_home_url' => __('Home url'), 'project_issues_url' => __('Issues url') , 'description' => __('Description'));
        register_column_headers('overview', $columns);
        $projects = $this->model->getProjects();
        echo '<div class="wrap">';
        echo '<h2>Project Manager</h2>';
        require_once ("pages/overview.php");
        echo "</div>";
    }
    
    public function overview_project ($id = NULL)
    {
		$columns = array('project' => __('Project') , 'description' => __('Description'));
        $projects = $this->model->getProjects($id);
        
        $out = '<div class="wrap">';
        $out .= '<table class="widefat" cellspacing="0">
					<thead>
						<tr>
							<th width="70%">Name</th>
							<th>Description</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th>Name</th>
							<th>Description</th>
						</tr>
					</tfoot>
					<tbody>';
		foreach ($projects as $project) {
            echo '<tr height="50" id="project-' . $project->id . '" valign="middle">';
            foreach ($columns as $column_name => $column_display_name) {
                switch ($column_name) {
                    case 'description':
                        $out .= '<td class="project-id" valign="middle">' .  $project->description . '</td>';    
                    	break;
                    case 'project':
                        $out .= '<td valign="middle"><strong>' .  $project->title . '</strong><br/>';
                        if(isset($project->project_home_url)) {
							$out .= 'Home url: <a href="' .  $project->project_home_url . '">' .  $project->project_home_url . '</a><br/>';
                        }
                        $all_tasks = $this->model->getTasks(13, $project->id);
                        $out .= 'Opened issues:' .  count($this->model->getTasks(14, $project->id)) . '; Total issues:' .  count($all_tasks) . '<br/>';
                        if(count($all_tasks) > 0) {
							$out .= 'Next release date:' .  $all_tasks[0]->end . '<br/><br/>';
                        }
                        $out .= $this->model->getProjectStatus($project->id);
                        $out .= '</td>';   
                    	break;
                    default:
						break;
                }
            }
        }
        $out .= '</tr></tbody></table>';
        $out .= '</div><br/><br/><br/>';
        
        return $out;
    }


    /**
     * Controller for wp-trac/projects
     */
    public function projects ()
    {
        global $wpdb;
        echo '<div class="wrap">';
        echo '<h2>Project Manager</h2>';
                
        $doaction = $_GET['action'] ? $_GET['action'] : $_GET['action2'];
        switch ($doaction) {
            case "create":
                $months = $this->months;
                $day = date("d");
                $month = date("F");
                $year = date("Y");
                require_once ("pages/projects-create.php");
                break;
            
            case "edit":
                $project = $this->model->getProject($_GET['id']);
                require_once ("pages/projects-edit.php");
                break;
            
            default:
                switch ($doaction) {
                    case "_create":
                        $this->model->createProject($_POST);
                        break;
                    case "_update":
                        $this->model->updateProject($_POST);
                        break;
                    case "delete":
                        if (isset($_GET['check'])) {
                            foreach ((array) $_GET['check'] as $id) {
                                $this->model->deleteProject($id);
                            }
                            echo '<div class="updated fade below-h2" id="message"><p>Projects deleted.</p></div>';
                        } else {
                            $this->model->deleteProject($_GET['id']);
                            echo '<div class="updated fade below-h2" id="message"><p>Project deleted.</p></div>';
                        }
                        break;
                    case "complete":
                        if (isset($_GET['check'])) {
                            foreach ((array) $_GET['check'] as $id) {
                                $this->model->completeProject($id);
                            }
                            echo '<div class="updated fade below-h2" id="message"><p>Projects completed.</p></div>';
                        } else {
                            $this->model->completeProject($_GET['id']);
                            echo '<div class="updated fade below-h2" id="message"><p>Project completed.</p></div>';
                        }
                        break;
                }
                $projects = $this->model->getProjects();
                require_once ("pages/projects.php");
        }
        echo "</div>";
    }


    public function tasks ()
    {
        global $wpdb;
        echo '<div class="wrap">';
        echo '<h2>Project Manager</h2>';
        
        $doaction = $_REQUEST['action'] ? $_REQUEST['action'] : $_REQUEST['action2'];
        switch ($doaction) {
            case "Add Task":
            	list($day, $month, $year) = array(date("d"), date("F"), date("Y"));
            	$months = $this->months;
                $projects = $this->model->getProjects();
                $users = $this->model->getUsers();
                require_once ("pages/tasks-create.php");
                break;
            
            case "edit":
                $months = $this->months;
                $task = $this->model->getTask($_REQUEST['id']);
                $projects = $this->model->getProjects();
                $users = $this->model->getUsers();
                list ($start_year, $start_month, $start_day) = explode("-", $task->start);
                list ($end_year, $end_month, $end_day) = explode("-", $task->end);
                require_once ("pages/tasks-edit.php");
                break;
            
            default:
                switch ($doaction) {
                    case "_create":
                        $this->model->createTask($_POST);
                        break;
                    case "_update":
                        $this->model->updateTask($_POST);
                        break;
                    case "delete":
                        if (isset($_REQUEST['check'])) {
                            foreach ((array) $_REQUEST['check'] as $id) {
                                $this->model->deleteTask($id);
                            }
                            echo '<div class="updated fade below-h2" id="message"><p>Tasks deleted.</p></div>';
                        } else {
                            $this->model->deleteTask($_REQUEST['id']);
                            echo '<div class="updated fade below-h2" id="message"><p>Task deleted.</p></div>';
                        }
                        break;
                    case "complete":
                        if (isset($_REQUEST['check'])) {
                            foreach ((array) $_REQUEST['check'] as $id) {
                                $this->model->completeTask($id);
                            }
                            echo '<div class="updated fade below-h2" id="message"><p>Projects completed.</p></div>';
                        } else {
                            $this->model->completeTask($_REQUEST['task_id']);
                            echo '<div class="updated fade below-h2" id="message"><p>Project completed.</p></div>';
                        }
                        break;
                }
                $task_columns = array('cb' => '<input type="checkbox" />', 
                                    'name' => __('Name'),
                					'user' => __('User'),  
            						'start' => __('Start'),
            						'end' => __('End'),
                                    'project' => __('Project') , 
                                    'priority' => __('Priority'),
            						'complete' => __('Complete'));

                register_column_headers('task-manager', $task_columns);
                $list_mode = esc_url(add_query_arg('mode', 'list', remove_query_arg('action', $_SERVER['REQUEST_URI'])));
                $excerpt_mode = esc_url(add_query_arg('mode', 'excerpt', remove_query_arg('action', $_SERVER['REQUEST_URI'])));
                $projects = $this->model->getProjects();
                list($id, $pid) = explode("-", $_POST['filter']);
                $tasks = $this->model->getTasks($id, $pid);
                require_once ("pages/tasks.php");
        }
    }


    public function short_code ($attr)
    {
    	return $this->model->buildProjectStatus($attr['id']);
    }
}

?>
