<?php

define("PROJECTS_TABLE", "wp-trac-projects");
define("TASKS_TABLE", "wp-trac-tasks");

class AdvTracModel
{

    private $version = 1;


    /**
     * @param mixed $args
     */
    public function createProject (array $args)
    {
        global $wpdb;
        $wpdb->show_errors();
        
        $table = $wpdb->prefix . PROJECTS_TABLE;
        
        $start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
                        
        $data = array('title' => $args['title'],
        			  'description' => $args['description'],
        			  'start' => $start_date);
        			  
        if(strlen($args['end_year']) > 0  && strlen($args['end_year']) > 0 && strlen($args['end_year']) > 0) {
			$end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
			$data['end'] = $end_date;
		}
		
		if(strlen($args['project_home_url']) > 0) {
			$data['project_home_url'] = $args['project_home_url'];
		}
		
		if(strlen($args['project_issues_url']) > 0) {
			$data['project_issues_url'] = $args['project_issues_url'];
		}
        
        $wpdb->insert( $table, (array) $data );
    
    }
    
    /**
     * @param mixed $args
     */
    public function updateProject (array $args)
    {
        global $wpdb;
        $wpdb->show_errors();
        
        $table = $wpdb->prefix . PROJECTS_TABLE;
        
        $start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
        
        $data = array('title' => $args['title'],
        			  'description' => $args['description'],
        			  'start' => $start_date);
        			  
        if(strlen($args['end_year']) > 0  && strlen($args['end_year']) > 0 && strlen($args['end_year']) > 0) {
			$end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
			$data['end'] = $end_date;
		}
		
		if(strlen($args['project_home_url']) > 0) {
			$data['project_home_url'] = $args['project_home_url'];
		}
		
		if(strlen($args['project_issues_url']) > 0) {
			$data['project_issues_url'] = $args['project_issues_url'];
		}
  
        $wpdb->update( $table, (array) $data, (array) array('id' => $args['project_id']) );
    }

    /**
     * Delete a single project and all tasks associated with it.
     * @param int $id
     */
    public function deleteProject ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table = $wpdb->prefix . PROJECTS_TABLE;
        
        $sql = "DELETE FROM `".$table."` WHERE `id` = '".$id."'";
        $wpdb->query($sql);
        $table = $wpdb->prefix . "tasks";
        $sql = "DELETE FROM `".$table."` WHERE `pid` = '".$id."'";
        $wpdb->query($sql);
    }

    /**
     * @todo implement filtering
     * @todo Valery: Do not see any need in filtering, but it's better to be
     * possible get one project by id
     */
    public function getProjects ($id = NULL)
    {
        global $wpdb;
        $table = $wpdb->prefix . PROJECTS_TABLE;
        if (isset($id)) {
			$id = $wpdb->escape($id);
			
            $sql = "SELECT * FROM `".$table."` WHERE `id` = '".$id."'";
        } else {
            $sql = "SELECT * FROM `".$table."`";
        }
        return $wpdb->get_results($sql, OBJECT);
    }

    /**
     * @param int $id
     */   
    public function getProject ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table = $wpdb->prefix . PROJECTS_TABLE;
        $sql = "SELECT * FROM `".$table."` WHERE `id` = '".$id."'";
        return $wpdb->get_row($sql, OBJECT);
    }
    
    /**
     * @param int $id
     */ 
    public function completeProject ($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . TASKS_TABLE;
        $wpdb->update( $table, (array) array('complete' => 100), (array) array('pid' => $id) );   
    }
        
    /**
     * @param mixed $args
     */     
    public function createTask (array $args)
    {
        global $wpdb;
        $start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
        $end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
        
        $data = array('pid' => $args['project'],
        			  'uid' => $args['user'],
                      'title' => $args['title'],
                      'description' => $args['description'], 
                      'start' => $start_date, 
                      'end' => $end_date,
         			  'priority' => $args['priority'],
        			  'complete' => $args['complete']);
        
        $table = $wpdb->prefix . TASKS_TABLE;
        
        $wpdb->insert( $table, (array) $data );
    }

    /**
     * @param mixed $args
     */     
    public function updateTask (array $args)
    {
        global $wpdb;
        $table = $wpdb->prefix . TASKS_TABLE;
        
        $start_date = $args['start_year'] . "-" . $args['start_month'] . "-" . $args['start_day'];
        $end_date = $args['end_year'] . "-" . $args['end_month'] . "-" . $args['end_day'];
        
        $data = array('pid' => $args['project'],
        			  'uid' => $args['user'],
        			  'title' => $args['title'],
        			  'description' => $args['description'],
        			  'start' => $start_date,
        			  'end' => $end_date,
        			  'priority' => $args['priority'],
        			  'complete' => $args['complete']);
  		
        $wpdb->update($table, (array) $data, (array) array('id' => $args['id']));
    }

    /**
     * @param int $id
     */ 
    public function deleteTask ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table = $wpdb->prefix . TASKS_TABLE;
        $sql = "DELETE FROM `".$table."` WHERE `id` = '".$id."'";
        $wpdb->query($sql);
    }

    /**
     * @param int $id
     */     
    public function completeTask ($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . TASKS_TABLE;
        $wpdb->update($table, (array) array('complete' => 100), (array) array('id' => $id));
    }

    public function getTasks ($id, $pid)
    {
        global $wpdb;
        $table = $wpdb->prefix . TASKS_TABLE;
        $id = $wpdb->escape($id);
        $pid = $wpdb->escape($pid);
        
        switch($id) {
            case 1:
            	$query = "SELECT * FROM `".$table."` WHERE `complete` = 100";
            break;

            case 2:
            	$query = "SELECT * FROM `".$table."` WHERE `complete` < 100";
            break;
            
            case 3:
            	$query = "SELECT * FROM `".$table."` ORDER BY `priority` DESC";
            break;

            case 4:
            	$query = "SELECT * FROM `".$table."` ORDER BY `start` DESC";
            break;
            
            case 5:
            	$query = "SELECT * FROM `".$table."` ORDER BY `end` DESC";
            break;

            case 6:
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."'";
            	break;
            	
            case 7:
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."' AND `complete` = 100";
            	break;
            	
            case 8:
            	$query = "SELECT * FROM `".$table."` WHERE `complete` < 100 AND  `pid` = '".$pid."'";
            break;
            
            case 9:
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."' ORDER BY `priority` DESC";
            break;

            case 10:
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."' ORDER BY `start` DESC";
            break;
            
            case 11:
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."' ORDER BY `end` DESC";
            break;
            
			case 12:
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."' ORDER BY `end`,`start`";
            break;
            	
            case 13:
				$today = date("Y-m-d");
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."' AND ('".$today."' between `start` AND `end`) ORDER BY `end` DESC";
            	break;
            	
            case 14:
				$today = date("Y-m-d");
            	$query = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."' AND ('".$today."' between `start` AND `end`) AND `complete` < 100  ORDER BY `end` DESC";
            	break;
            	
            default:
            	$query = "SELECT * FROM `".$table."`";
        }
		
        return $wpdb->get_results($query, OBJECT);
    }
    
    public function buildProjectStatus($id = NULL)
    {
		global $wpdb;
        $wpdb->show_errors();
    	echo $this->getProjectStatus($id);
    }
    
    public function getProjectStatus($id = NULL)
    {
    	$out = "";
        
    	$projects = $this->getProjects($id);
    	
    	foreach($projects as $project) {
    		$tasks = $this->getTasks(13, $project->id);
    		if(count($tasks) > 0) {

                $out .= '<table class="widefat" cellspacing="0">
                		<thead>
                            <tr>
								<th width="100">Name</th>
                                <th width="400">Progress</th>
                                <th>&nbsp</th>
                            </tr>
                        </thead>';
                        
                        
            	foreach($tasks as $task) {
            		if(!empty($task->id)) {
                		$out .= '<tr class="row">
            	                    <td>'.$task->title.'</td>
            	                    <td>
            	                        <div id="progress-'.$id.'-'.$task->id.'" class="progressbar"></div>                                                                         
            	                    </td>
            	                    <td>'.$task->complete.'%</td>
            	                </tr>';
                		$script .= '$("#progress-'.$id.'-'.$task->id.'").progressbar({value: '.$task->complete.'});';
            		}
            	
            	}
            	
            	$out .= '</table><br />';    		
        	
        	}
    	}
    	$out .= '<script type="text/javascript">$(function() {'.$script.'});</script>';
    	
		return $out;
    }
    
    
//==============================================================================

    public function taskStatus ($id)
    {
        global $wpdb;
        
        $id = (int) $wpdb->escape($id);
        $table = $wpdb->prefix . TASKS_TABLE;
        
        $sql = "SELECT COUNT(*) FROM `".$table."` WHERE `pid`='".$id."'";
        $count = $wpdb->query($sql);
        $sql = "SELECT COUNT(`id`) FROM `".$table."` WHERE pid='".$id."' AND `complete`='100'";
        $count['complete'] = $wpdb->query($sql);
        $count['incomplete'] = $count['total'] - $count['complete'];
        return $count;
    
    }
    
    public function getProjectTasks ($pid)
    {
        global $wpdb;
        $pid = (int) $wpdb->escape($pid);
        $table = $wpdb->prefix . TASKS_TABLE;
        $sql = "SELECT * FROM `".$table."` WHERE `pid` = '".$pid."'";
        
        return $wpdb->get_results($sql, OBJECT);
    }

    public function getTask ($id)
    {
        global $wpdb;
        $id = (int) $wpdb->escape($id);
        $table = $wpdb->prefix . TASKS_TABLE;
        $sql = "SELECT * FROM `".$table."` WHERE `id` = '".$id."'";
        return $wpdb->get_row($sql, OBJECT);
    }

    public function getUsers ()
    {
        global $wpdb;
        $table = $wpdb->prefix . "users";
        $sql = "SELECT `id`,`user_nicename` FROM `".$table."`";
        return $wpdb->get_results($sql, OBJECT);
    }

    public function getUser ($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . "users";
        $sql = "SELECT `user_nicename` FROM `".$table."` WHERE `id` = '".$id."'";
        return $wpdb->get_row($sql, OBJECT);
    }
    
}
?>
