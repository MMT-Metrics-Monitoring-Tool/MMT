<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\I18n\Time;

class MobileController extends AppController
{

    public function index() {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
            } else {
                $this->Flash->error('Your username or password is incorrect.');
            }
        }
        
        $myProjects = [];

        if ($this->Auth->user()) {
            $myProjectIds = TableRegistry::get('Members')
                ->find()
                ->where(['user_id' => $this->Auth->user('id')])
                ->select('project_id')
                ->toArray();
           
            $ids = [];
           
            foreach ($myProjectIds as $item) {
                $ids[] = $item->project_id;
            }
           
            $myProjects = TableRegistry::get('Projects')->find()->where(['id IN' => $ids])->toArray();
        }

        $this->set('myProjects', $myProjects);
    }
    
    public function addhour() {
        $worktypes = TableRegistry::get('Workinghours')->Worktypes->find('list', ['limit' => 200]);
        $workinghour = TableRegistry::get('Workinghours')->newEntity([]);
        
        if ($this->request->is('post')) {
            // get data from the form
            $workinghour = TableRegistry::get('Workinghours')->patchEntity($workinghour, $this->request->getData());
            // only allow members to add workinghours for themself
            $workinghour['member_id'] = $this->request->getSession()->read('selected_project_memberid');
            
            if (TableRegistry::get('Workinghours')->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'project']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }
        
        $this->set('worktypes', $worktypes);
        $this->set('workinghour', $workinghour);
    }
    
    public function project($id = null) {
        if ($id != null) {
            $project = TableRegistry::get('Projects')->get($id, [
                'contain' => ['Members', 'Metrics', 'Weeklyreports']
            ]);
            
            $this->request->getSession()->write('selected_project', $project);
        } else {
            $id = $this->request->getSession()->read('selected_project')['id'];
        }
       
        $members = TableRegistry::get('Members')->find('all', [
                'conditions' => ['project_id' => $id],
                'contain' => ['Users', 'Projects', 'Workinghours']
                ])->toArray();
        
        $this->set('members', $members);
    }

    public function report() {
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        
        $report = TableRegistry::get('Weeklyreports')->find('all', [
            'conditions' => ['project_id' => $project_id],
            'order' => ['year' => 'DESC', 'week' => 'DESC'],
            'contain' => ['Projects', 'Metrics', 'Workinghours']
            ])->first();
         
        $members = array();
        
        //get the weekly risks of the report
        $risks = array();
        
        if ($report !== null) {
            $metricNames = (new MetricsController())->getMetricNames();
            
            foreach ($report->metrics as $metrics) {
                $metrics['metric_description'] = $metricNames[$metrics->metrictype_id];
            }

            $risksController = new RisksController();
            $riskTypes = $risksController->getSeverityProbTypes();
            $currentWeeklyRisks = TableRegistry::get('Weeklyrisks')
                ->find()
                ->where(['weeklyreport_id' => $report['id']]);

            foreach ($currentWeeklyRisks as $weeklyRisk) {
                $risk = new \stdClass();

                $risk->description = TableRegistry::get('Risks')->get($weeklyRisk['risk_id'])['description'];
                $risk->severity = $riskTypes[$weeklyRisk['severity']];
                $risk->probability = $riskTypes[$weeklyRisk['probability']];

                $risks[] = $risk;
            }
            
            $membersList = TableRegistry::get('Members')->find('all', [
                'conditions' => ['project_id' => $project_id],
                'contain' => ['Users', 'Projects', 'Workinghours']
                ])->toArray();
            
            foreach ($membersList as $member) {
                if ($member->project_role === 'senior_developer' || $member->project_role === 'developer') {
                    $name = $member->user->full_name;
                    $hours = 0;
                    $queryForHours = $member->workinghours;
                    
                    foreach ($queryForHours as $key) {
                        if ($report->week == $key->date->format('W')) {
                            if (($report->week == 52 && $key->date->format('m') == 01) ||
                                ($report->week == 5 && $key->date->format('m') == 01) ||
                                ($report->week == 1 && $key->date->format('m') == 12) ||
                                ($report->year == $key->date->format('Y'))) {
                                $hours += $key->duration;
                            }
                        }
                    }
                    
                    $obj = new \stdClass();
                    $obj->name = $name;
                    $obj->hours = $hours;
                    $obj->role = $member->project_role;
                    
                    $members[] = $obj;
                }
            }
        }

        $this->set('report', $report);
        $this->set('risks', $risks);
        $this->set('members', $members);
    }
    
    public function stat() {
        // get the limits from the sidebar if changes were submitted
        if ($this->request->is('post')) {
            $data = $this->request->getData();
 
            // fetch values using helpers
            $min = $data['weekmin'];
            $max = $data['weekmax'];
            $year = $data['year'];
            
            // correction for nonsensical values for week numbers
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
            
            $statistics_limits['weekmin'] = $min;
            $statistics_limits['weekmax'] = $max;
            $statistics_limits['year'] = $year;
            
            $this->request->getSession()->write('statistics_limits', $statistics_limits);
        }
        
        // current default settings
        if (!$this->request->getSession()->check('statistics_limits')) {
            $time = Time::now();
            $week = date('W');
            $month = date('m');
            // weekmin will be the current week - 10
            // weekmax will be the current week + 1
            // exceptions when the current week is 1-10 or 53
            
            // weeks 2-10
            if ($week >= 2 && $week <= 10) {
                $weekmin = 1;
                $weekmax = $week+1;
            } elseif ($week == 1) { // week 1
                $weekmin = 43;
                $weekmax = 53;
            } elseif ($week == 53) {
                $weekmin = $week-10;
                $weekmax = $week;
            } else { // weeks 11-52
                $weekmin = $week-10;
                $weekmax = $week+1;
            }
            // these initial limits are arbitrary so change freely if needed
            $statistics_limits['weekmin'] = $weekmin;
            $statistics_limits['weekmax'] = $weekmax;
            
            $year = $time->year;
            $diffYear = $year - 1 ;
            
            if (($week == 1 && $month == 01) ||
                ($week == 52 && $month == 01) ||
                ($week == 53 && $month == 01)) {
                $statistics_limits['year'] = $diffYear;
            } else {
                $statistics_limits['year'] = $time->year;
            }
                    
            $this->request->getSession()->write('statistics_limits', $statistics_limits);
        }

        // load the limits to a variable
        $statistics_limits = $this->request->getSession()->read('statistics_limits');
        // function in the projects table "ProjectsTable.php"
        // return the list of projects
        
        $projectsTable = TableRegistry::get('Projects');
        $userRole = $this->request->getSession()->read('Auth.User.role');
        $userProjects = array();
          
        // If the user isn't admin, shows only projects, which the user is part of.
        if ($userRole != 'admin') {
            $userId = $this->request->getSession()->read('Auth.User.id');
            $members = TableRegistry::get('Members');
            $projectIds = $members
                ->find()
                ->select(['project_id'])
                ->where(['user_id' => $userId])
                ->toArray();

            // Selects only projects where the user is active member of the project meaning that the member's ending date in that
            // that project hasn't expired.
            $projectIds = array();
            foreach ($allProjectIds as $temp) {
                if (in_array($temp['project_id'], $this->request->getSession()->read('project_memberof_list'))) {
                    $projectIds[] = $temp;
                }
            }
        } else {
            // If user is admin, shows all the projects.
            $userProjects = $projectsTable->getAllProjects();
        }

        // Goes through all the user's projects if user has any.
        if (!empty($projectIds)) {
            $userProjects = $projectsTable->getUserProjects($projectIds);
        }
        
        $projects = array();
        // the weeklyreport weeks and the total weeklyhours duration is loaded for all projects
        // functions in "ProjectsTable.php"
        foreach ($userProjects as $project) {
            $project['reports'] = $projectsTable->getWeeklyreportWeeks(
                $project['id'],
                $statistics_limits['weekmin'],
                $statistics_limits['weekmax'],
                $statistics_limits['year']
            );
            $project['duration'] = $projectsTable->getWeeklyhoursDuration($project['id']);
            $project['sum'] = $projectsTable->getHoursDuration($project['id']);
            $projects[] = $project;
        }

        // the projects and their data are made visible in the "statistics.php" page
        $this->set(compact('projects'));
        $this->set('_serialize', ['projects']);
    }
    
    public function logout() {
        // remove all session data
        $this->request->getSession()->delete('selected_project');
        $this->request->getSession()->delete('selected_project_role');
        $this->request->getSession()->delete('selected_project_memberid');
        $this->request->getSession()->delete('current_weeklyreport');
        $this->request->getSession()->delete('current_metrics');
        $this->request->getSession()->delete('current_weeklyhours');
        $this->request->getSession()->delete('project_list');
        $this->request->getSession()->delete('project_memberof_list');
        $this->request->getSession()->delete('is_admin');
        $this->request->getSession()->delete('is_supervisor');
        // Removes selected chart limitations (weekmin,weekmax,yearmin,yearmax)
        //$this->request->getSession()->delete('chartLimits');
        
        $this->Flash->success('You are now logged out.');
        
        $this->Auth->logout();
        
        return $this->redirect(['action' => 'index']);
    }
    
    // this allows anyone to go to the frontpage
    public function beforeFilter(\Cake\Event\EventInterface $event) {
        $this->Auth->allow(['index']);
        
        if ($this->Auth->user()) {
            $this->Auth->allow(['chart']);
            $this->Auth->allow(['logout']);
            $this->Auth->allow(['stat']);
        }
    }
    
    public function beforeRender(\Cake\Event\EventInterface $event) {
        $this->viewBuilder()->layout('mobile');
    }
    
    
    public function isAuthorized($user) {
        // authorization for the selected project
        if ($this->request->getParam('action') === 'project') {
            if (!empty($this->request->getParam('pass'))) {
                $id = $this->request->getParam('pass')[0];
            } else {
                $id = $this->request->getSession()->read('selected_project')['id'];
            }

            $time = Time::now();
            $project_role = "";
            $project_memberid = -1;
            // what kind of member is the user
            $members = TableRegistry::get('Members');
            // load all the memberships that the user has for the selected project
            $query = $members
                ->find()
                ->select(['project_role', 'id', 'ending_date'])
                ->where(['user_id =' => $this->Auth->user('id'), 'project_id =' => $id])
                ->toArray();

            // for loop goes through all the memberships that this user has for this project
            // its most likely just 1, but since it has not been limited to that we must check for all possibilities
            // the idea is that the highest membership is saved,
            // so if he or she is a developer and a supervisor, we save the latter
            foreach ($query as $temp) {
                // if supervisor, overwrite all other memberships
                if ($temp->project_role == "supervisor" && ($temp->ending_date > $time || $temp->ending_date == null)) {
                    $project_role = $temp->project_role;
                    $project_memberid = $temp->id;
                } elseif ($temp->project_role == "senior_developer" && $project_role != "supervisor" && ($temp->ending_date > $time || $temp->ending_date == null)) {
                    // if the user is a senior developer in the project
                    // but we have not yet found out that he or she is a supervisor
                    // if dev or null then it gets overwritten
                    $project_role = $temp->project_role;
                    $project_memberid = $temp->id;
                } elseif ($project_role != "supervisor" && $project_role != "senior_developer" && ($temp->ending_date > $time || $temp->ending_date == null)) {
                    // if we have not found out that the user is a senior developer or a supervisor
                    $project_role = $temp->project_role;
                    $project_memberid = $temp->id;
                }
            }
            // if the user is a admin, he is automatically a admin of the project
            if ($this->Auth->user('role') == "admin") {
                $project_role = "admin";
            } elseif ($project_role == "") { // if the user is not a admin and not a member
                $project_role = "notmember";
            }

            $this->request->getSession()->write('selected_project_role', $project_role);
            $this->request->getSession()->write('selected_project_memberid', $project_memberid);

            // if the user is not a member of the project he can not access it
            if ($project_role == "notmember") {
                return false;
            } else {
                return true;
            }
        } else if ($this->request->getParam('action') === 'addhour') {
            $project_role = $this->request->getSession()->read('selected_project_role');
            
            return ($project_role == 'senior_developer' || $project_role == 'developer');
        } else {
            // Default allow
            return true;
        }
    }
}
