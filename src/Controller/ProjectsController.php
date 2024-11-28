<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\MemberController;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Routing\Route\RedirectRoute;

class ProjectsController extends AppController
{

    public function index() {

        // Clear selected project
        if ($this->Auth->user('id') != null) {
            $this->request->getSession()->write('selected_project', null);
        }

        // List of the projects that should be shown in the front page
        $userId = $this->Auth->user('id');
        $userRole = $this->Auth->user('role');
        $userProjectList = $this->request->getSession()->read('project_memberof_list') ?? []; // Users projects

        // check if admin
        if ($userRole === 'admin') {
            // Admin sees as projects
            $this->paginate = [
                'limit' => 25,
                'order' => ['created_on' => 'DESC']
            ];
        }  else {
        // Ordinary users only see their own projects or empty pages
            if (!empty($userProjectList)) {
                $this->paginate = [
                    'conditions' => ['id IN' => $userProjectList], // Limit projects to user's projects
                    'limit' => 25,
                    'order' => ['created_on' => 'DESC']
                ];
            } 
            if (empty($userProjectList)) {
                $this->paginate = [
                    'limit' => 10000000
                ];
            } 
        }
    
        // Redirect user to working hours if they have only one project
        if (count($userProjectList) == 1 && $userRole == 'user' 
                && $this->request->getSession()->read('first_view') == true) {
            return $this->redirect([
                'controller' => 'Projects', 
                'action' => 'view', 
                (string)$userProjectList[0]
            ]);
        }
    
        $mobileOptional = false;
    
        $this->set('projects', $this->paginate($this->Projects));
        $this->set('_serialize', ['projects']);
        $this->set('mobileOptional', $mobileOptional);
    }
    
    // function that is run when you select a project
    public function view($id = null) {
        $project = $this->Projects->get($id, [
            'contain' => ['Members', 'Metrics', 'Weeklyreports']
        ]);

        // when normal user opens the project first time after login it redirect to Workinghours
        if ($this->Auth->user('role') == 'user' && $this->request->getSession()->read('first_view') == true) {
            $this->request->getSession()->write('first_view', false);
            $this->request->getSession()->write('selected_project', $project);
            return $this->redirect(
                ['controller' => 'Workinghours', 'action' => 'index']
            );
        }

        $this->set('project', $project);
        $this->set('_serialize', ['project']);
        $selectedProjectId = $this->request->getSession()->read('selected_project')['id'] ?? null;
        $projectId = $project['id'] ?? null;
        // if the selected project is a new one
        if ($projectId && $selectedProjectId != $projectId) {
            // write the new id
            $this->request->getSession()->write('selected_project', $project);
            // remove the all data from the weeklyreport form if any exists
            $this->request->getSession()->delete('current_weeklyreport');
            $this->request->getSession()->delete('current_metrics');
            $this->request->getSession()->delete('current_weeklyhours');
        }
    }
    
    public function statistics() {
        // get the limits from the sidebar if changes were submitted
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            /* FIX: editing limits on Public Statistics now behaves like a decent UI
             */
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
            
            /*
             * REMOVED after deemed too restricting, but I left the code so it can easily be implemented if needed
             * remember to also change similar code in Template/Projects/statistics.ctp
             *
            // for clear displaying purposes, amount of columns is limited to 11 (name + 10 weeks)
            if ( ($max - $min) > 9 ) {
                $max = $min + 9;
                $this->Flash->success(__('Maximum of ten weeks can be displayed at a time.'));
            }
             *
             *
             */
                
            // correction of year to current if bigger than it
            /*if ( $year > date("Y") ) {
                $year = date("Y");
            }*/
            
            $statistics_limits['weekmin'] = $min;
            $statistics_limits['weekmax'] = $max;
            $statistics_limits['year'] = $year;
            
            $this->request->getSession()->write('statistics_limits', $statistics_limits);
            // reload page
            $page = $_SERVER['PHP_SELF'];
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

        // Searches for user's projects in which he is part of and uses function
        // getUserProjects in ProjectsTable.php to return all the needed information
        // to show statistics on statistics.php.
        $userRole = $this->request->getSession()->read('Auth.User.role');
        $userProjects = array();
          
        // If the user isn't admin, shows only projects, which the user is part of.
        if ($userRole != 'admin') {
            $userId = $this->request->getSession()->read('Auth.User.id');
            $members = TableRegistry::get('Members');
            $allProjectIds = $members
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
            $userProjects = $this->Projects->getAllProjects();
        }

        // Goes through all the user's projects if user has any.
        if (!empty($projectIds)) {
            $userProjects = $this->Projects->getUserProjects($projectIds);
        }
        
        $projects = array();

        // the weeklyreport weeks and the total weeklyhours duration is loaded for all projects
        // functions in "ProjectsTable.php"
        foreach ($userProjects as $project) {
            $project['reports'] = $this->Projects->getWeeklyreportWeeks(
                $project['id'],
                $statistics_limits['weekmin'],
                $statistics_limits['weekmax'],
                $statistics_limits['year']
            );
            $project['startDate'] = $this->Projects->getStartDate($project['id']);
            $project['endDate'] = $this->Projects->getEndDate($project['id']);
            $project['totalHours'] = $this->Projects->getTotalHours($project['id']);
            $project['targetHours'] = $this->Projects->getTargetHours($project['id']);
            $project['userMembersCount'] = $this->Projects->getUserMembersCount($project['id']);
            $project['metrics'] = $this->Projects->getMetrics($project['id']);
            $project['risks'] = $this->Projects->getRisks($project['id']);
            $project['minimumHours'] = $this->Projects->getMinimumHours($project['id']);
            $project['earliestLastSeenDate'] = $this->Projects->getEarliestLastSeenDate($project['id']);

            $admin = $this->request->getSession()->read('is_admin');
            $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            
            if ($admin || $supervisor) {
                $project['statusColors'] = $this->Projects->getStatusColors($project['id'], $project['metrics']);
                $project['earnedValueData'] = $this->Projects->getEarnedValueData($project['id']);
            }
            
            $projects[] = $project;
        }

        // the projects and their data are made visible in the "statistics.php" page
        $this->set(compact('projects'));
        $this->set('_serialize', ['projects']);
    }
    
    // empty function, because nothing needs to be done but the function has to exist
    public function faq() {
    }
    
    // empty function, because nothing needs to be done but the function has to exist
    public function about() {
    }

    // empty function, because nothing needs to be done but the function has to exist
    public function accessibilitynotes() {
    }
    // empty function, because nothing needs to be done but the function has to exist
    public function publications() {
    }

    // empty function, because nothing needs to be done but the function has to exist
    public function privacy() {
    }
    

    public function add() {
        $project = $this->Projects->newEntity([]);
        if ($this->request->is('post')) {
            // data loaded from the form
            $project = $this->Projects->patchEntity($project, $this->request->getData());

            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));
                // if the project was not saved by an admin
                if ($this->Auth->user('role') != "admin") {
                    // the user is added to the project as a supervisor
                    $Members = TableRegistry::get('Members');
                    $Member = $Members->newEntity([]);
                    $Member['user_id'] = $this->Auth->user('id');
                    $Member['project_id'] = $project['id'];
                    $Member['project_role'] = "supervisor";
                    $Member['target_hours'] = null;

                    if (!$Members->save($Member)) {
                        $this->Flash->error(__('The project was saved, but we were not able to add you as a member'));
                    }
                }
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The project could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('project'));
        $this->set('_serialize', ['project']);
    }

    public function edit($id = null) {
        $project = $this->Projects->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            // data from the form
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            // updated_on date is placed automatically
            $time = Time::now();
            $project['updated_on'] = $time;
            
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));
                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('The project could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('project'));
        $this->set('_serialize', ['project']);

        // This is to force refresh modified project data in selected project variable
        $project = $this->Projects->get($id, [
            'contain' => ['Members', 'Metrics', 'Weeklyreports']
        ]);

        $this->request->getSession()->write('selected_project', $project);
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $project = $this->Projects->get($id);

        // check for dependent records
        $membersTable = TableRegistry::getTableLocator()->get('Members');
        $members = $membersTable->find()->where(['project_id' => $id])->toArray();

        if (!empty($members)) {
            // delete dependent records
            foreach ($members as $member) {
                $membersTable->delete($member);
            }
        }
        
        if ($this->Projects->delete($project)) {
            $this->Flash->success(__('The project has been deleted.'));
        } else {
            $this->Flash->error(__('The project could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    // this allows anyone to go to the frontpage
    public function beforeFilter(\Cake\Event\EventInterface $event) {
        // if the current user is not a logged in user
        if ($this->Auth->user()) {
            $this->Auth->allow(['statistics']);
        }

        // faq and about are open pages to everyone. Statistics and projects can only be accessed
        // when logged in.
        $this->Auth->allow(['faq']);
        $this->Auth->allow(['about']);
        $this->Auth->allow(['accessibilitynotes']);
        $this->Auth->allow(['publications']);
        $this->Auth->allow(['privacy']);
    }
    
    public function isAuthorized($user) {
        // Inactive can only do what users who are not members can
        // Commented out, as inactive users should be able to view their own data
        /** 
        * if ($user['role'] === 'inactive') {
        *    return false;
        *}
        */
        
        // the admin can see all the projects
        if ($this->request->getParam('action') === 'index' && $user['role'] === 'admin') {
            $query = $this->Projects
                ->find()
                ->select(['id'])
                ->toArray();
            
            $project_list = array();

            foreach ($query as $temp) {
                $project_list[] = $temp->id;
            }
            
            $this->request->getSession()->write('is_admin', true);
            $this->request->getSession()->write('project_list', $project_list);
            $this->request->getSession()->write('project_memberof_list', $project_list);

            return true;
        }
        
        if ($this->request->getParam('action') === 'index') {
            $time = Time::now();
            $members = TableRegistry::get('Members');
            // find all the projects that the user is a member in
            $query = $members
                ->find()
                ->select(['project_id', 'ending_date', 'project_role'])
                ->where(['user_id =' => $this->Auth->user('id')])
                ->toArray();
            
            $is_supervisor = false;
            $project_list = array();

            foreach ($query as $temp) {
                // check if the user is a supervisor in any of the projects
                // and add the projects to the projectlist
                if ($temp->ending_date > $time || $temp->ending_date == null) {
                    $project_list[] = $temp->project_id;

                    if ($temp->project_role == 'supervisor') {
                        $is_supervisor = true;
                    }
                }
            }
            
            $this->request->getSession()->write('is_supervisor', $is_supervisor);
            $this->request->getSession()->write('project_memberof_list', $project_list);


            $this->request->getSession()->write('project_list', $project_list);
            
            return true;
        }
        
        // authorization for the selected project
        if ($this->request->getParam('action') === 'view') {
            $time = Time::now();
            $project_role = "";
            $project_memberid = -1;
            // what kind of member is the user
            $members = TableRegistry::get('Members');
            // load all the memberships that the user has for the selected project
            $query = $members
                ->find()
                ->select(['project_role', 'id', 'ending_date'])
                ->where(['user_id =' => $this->Auth->user('id'), 'project_id =' => $this->request->getParam('pass')[0]])
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
            } elseif ($project_role == "") {
                // if the user is not a admin and not a member
                $project_role = "notmember";
            }

            $this->request->getSession()->write('selected_project_role', $project_role);
            $this->request->getSession()->write('selected_project_memberid', $project_memberid);

            // If the user is not a member of the project he can not access it
            if ($project_role == "notmember") {
                return false;
            } else {
                return true;
            }
        }

        $project_role = $this->request->getSession()->read('selected_project_role');
        
        // supervisors can add new projects
        // This has its own query because if the user is a member of multiple projects
        // his current role might not be his highest one
        if ($this->request->getParam('action') === 'add') {
            if ($this->Auth->user('role') == "admin") {
                return true;
            }
            
            $members = TableRegistry::get('Members');
            
            $query = $members
                ->find()
                ->select(['project_role'])
                ->where(['user_id =' => $user['id']])
                ->toArray();

            foreach ($query as $temp) {
                if ($temp->project_role == "supervisor") {
                    return true;
                }
            }
        }

        // supervisors can edit their own projects
        if ($this->request->getParam('action') === 'edit' || $this->request->getParam('action') === 'delete') {
            //Senior developers can edit projects information, but not delete it
            if ($this->request->getParam('action') === 'edit' && $project_role == "senior_developer") {
                return true;
            }
            
            if ($project_role == "supervisor" || $project_role == "admin") {
                return true;
            }
        }
        
        // Default deny
        return false;
    }
}
