<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\Routing\Router;

class WorkinghoursController extends AppController
{

    public function index() {
        // only load workinghours from current project
        // ordered by date
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $this->paginate = [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id),
            'order' => ['date' => 'DESC']
        ];
        
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);

        $members = $this->Workinghours->Members->find('list', [
              'conditions' => ['Members.project_id' => $project_id,
                                'Members.project_role !=' => 'supervisor',
                                'and' => array('Members.project_role !=' => 'client')],
                               //'Members.project_role !=' => 'supervisor',
                               //'or' => array(''Members.ending_date >' => $now,'Members.ending_date IS' => NULL)],
              'contain' => ['Users'],
              'keyField' => 'id',
              'valueField' => 'user.full_name',
              'limit' => 200]);
        
        $this->set('workinghours', $this->paginate($this->Workinghours));
        $this->set(compact('memberlist', 'members'));
        $this->set('_serialize', ['workinghours']);

        $workinghour = $this->Workinghours->newEntity([]);

        if ($this->request->is('post')) {
            // get data from the form
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->getData());
            // only allow members to add workinghours for themself
            $workinghour['member_id'] = $this->request->getSession()->read('selected_project_memberid');
            
            if ($this->Workinghours->save($workinghour)
                && ($this->request->getSession()->read('selected_project_role') == 'senior_developer'
                || $this->request->getSession()->read('selected_project_role') == 'developer')) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }

        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $members = $this->Workinghours->Members->find('list', ['limit' => 200, 'conditions' => array('Members.project_id' => $project_id)]);
        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }
    
    // For listing member's workinghours
    public function tasks($id = null) {
        // only load workinghours from current project
        // ordered by date
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $this->paginate = [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id, 'Members.id' => $id),
            'order' => ['date' => 'DESC']
        ];
        
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);

        //$now = Time::now();
        $members = $this->Workinghours->Members->find('list', [
              'conditions' => ['Members.project_id' => $project_id, 'Members.id' => $id],
        ]);
        
        $this->set('workinghours', $this->paginate($this->Workinghours));
        $this->set(compact('memberlist', 'members'));
        $this->set('_serialize', ['workinghours']);
    }

    public function view($id = null) {
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $workinghour = $this->Workinghours->get($id, [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id)
        ]);
        
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);

        foreach ($memberlist as $member) {
            if ($workinghour->member->id == $member['id']) {
                // if the id's match add the name for the member
                $workinghour->member['member_name'] = $member['member_name'];
            }
        }

        $this->set('workinghour', $workinghour);
        $this->set('_serialize', ['workinghour']);
    }
    
    
    public function add() {
        $workinghour = $this->Workinghours->newEntity([]);

        if ($this->request->is('post')) {
            // get data from the form
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->getData());
            $time = Time::now();
            $workinghour['created_on'] = $time;
            // only allow members to add workinghours for themself
            $workinghour['member_id'] = $this->request->getSession()->read('selected_project_memberid');
            
            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }

        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $members = $this->Workinghours->Members->find('list', ['limit' => 200, 'conditions' => array('Members.project_id' => $project_id)]);
        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }
    
    public function addlate() {
        $workinghour = $this->Workinghours->newEntity([]);

        if ($this->request->is('post')) {
            // get data from the form
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->getData());
            $time = Time::now();
            $workinghour['created_on'] = $time;
            // only allow members to add workinghours for themself
            $workinghour['member_id'] = $this->request->getSession()->read('selected_project_memberid');
            
            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                // redirect back to the weeklyreport
                echo "<script>
                        window.history.go(-2);
                </script>";
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }

        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $members = $this->Workinghours->Members->find('list', ['limit' => 200, 'conditions' => array('Members.project_id' => $project_id)]);
        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }
    
    // senior developers, supervisors and admins can add workinghours for the developers
    public function adddev() {
        $workinghour = $this->Workinghours->newEntity([]);

        if ($this->request->is('post')) {
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->getData());
            $time = Time::now();
            $workinghour['created_on'] = $time;

            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }

        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $now = Time::now();
        $members = $this->Workinghours->Members->find('list', [
              'conditions' => ['Members.project_id' => $project_id, 'Members.project_role !=' => 'supervisor',
                                'and' => array('Members.project_role !=' => 'client')],
              'contain' => ['Users'],
              'keyField' => 'id',
              'valueField' => 'user.full_name',
              'limit' => 200]);

        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }
    
    public function edit($id = null) {
        // only allow editing workinghours from the current project
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $workinghour = $this->Workinghours->get($id, [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id)
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->getData());
            $time = Time::now();
            $workinghour['modified_on'] = $time;

            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }

        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $now = Time::now();
        $members = $this->Workinghours->Members->find('list', ['limit' => 200])
            ->where([
                'Members.project_id' => $project_id,
                'Members.project_role !=' => 'supervisor',
                'OR' => [
                    ['Members.ending_date >' => $now],
                    ['Members.ending_date IS' => null]
                ]
            ]);
        
        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $workinghour = $this->Workinghours->get($id);

        if ($this->Workinghours->delete($workinghour)) {
            $this->Flash->success(__('The workinghour has been deleted.'));
        } else {
            $this->Flash->error(__('The workinghour could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function export() {
        $this->response = $this->response->withDownload('workinghours_export.csv');
        $data = $this->Workinghours->find('all', ['contain' => ['Members', 'Worktypes']])->toArray();
        $parsedData = [];
        $tempData = [];

        foreach ($data as $value) {
            $project = TableRegistry::get('Projects')->get($value['member']['project_id']);
            $user = TableRegistry::get('Users')->get($value['member']['user_id']);

            // Check that exporting for research purposes is allowed.
            if ($user['research_allowed'] == 0) {
                // If not, continue to the next
                continue;
            }

            // Save data in more usable format (3d-array)

            // Check if array for project already exists
            if (!array_key_exists($project['project_name'], $tempData)) {
                $tempData[$project['project_name']] = [];
            }

            // Check if array for member already exists
            if (!array_key_exists($value['member_id'], $tempData[$project['project_name']])) {
                $tempData[$project['project_name']][$value['member_id']] = [];
            }


            // Check if array for worktype exists and sum duration
            if (!array_key_exists($value['worktype']['description'], $tempData[$project['project_name']][$value['member_id']])) {
                $tempData[$project['project_name']][$value['member_id']][$value['worktype']['description']] = [
                    'member_id' => $value['member_id'],
                    'project_name' => $project['project_name'],
                    'description' => $value['worktype']['description'],
                    'sum' => $value['duration']
                ];
            } else {
                $tempData[$project['project_name']][$value['member_id']][$value['worktype']['description']]['sum'] += $value['duration'];
            }
        }

        // Convert into final format (1d-array)
        foreach ($tempData as $i) {
            foreach ($i as $j) {
                foreach ($j as $t) {
                    $parsedData[] = $t;
                }
            }
        }

        $_header = ['project_name', 'member_id', 'description', 'sum'];
        $_extract = ['project_name', 'member_id', 'description', 'sum'];
        $_serialize = 'parsedData';

        $this->viewBuilder()->setClassName('CsvView.Csv');
        $this->set(compact('parsedData', '_serialize', '_header', '_extract'));
    }

    public function isAuthorized($user) {

        if ($user['role'] === 'inactive') {
            $this->Auth->setConfig('authError', false);
            $this->Flash->error(__('Inactive users are not authorized to access this page.'));
            return false;
        }
        
        // admins can do anything
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        $project_role = $this->request->getSession()->read('selected_project_role');
        
        if ($this->request->getParam('action') === 'add') {
            // supervisor cannot have workinghours, and the add function simply takes the member_id of the current user
            if ($project_role != "notmember" && $project_role != "supervisor" && $project_role != "client") {
                return true;
            }
            return false;
        }
        // developers can only edit and delete their own workinghours
        if ($this->request->getParam('action') === 'edit' || $this->request->getParam('action') === 'delete') {
            if ($project_role == "developer") {
                $query = $this->Workinghours
                    ->find()
                    ->select(['member_id'])
                    ->where(['id =' => $this->request->getParam('pass')[0]])
                    ->toArray();
                
                // fetching the Members-table from database
                $members = TableRegistry::get('Members');
                
                // querying Members-table for member ID:s that correspond to the current user's ID
                $query2 = $members
                    ->find()
                    ->select(['id'])
                    ->where(['user_id =' => $user['id']])
                    ->toArray();

                foreach ($query2 as $temp) {
                    if ($query[0]->member_id == $temp->id) {
                        return true;
                    }
                }

                return false;
            }
        }

        //special rule for workinghours controller.
        // supervisors cannot log time late
        if ($this->request->getParam('action') === 'addlate') {
            return false;
        }
        if ($this->request->getParam('action') === 'tasks') {
            if ($project_role == "senior_developer" || $project_role == "supervisor" || $project_role == "developer" || $project_role == "client") {
                return true;
            }
        }
        //all members can add edit and delete workinghours
        if ($this->request->getParam('action') === 'adddev' || $this->request->getParam('action') === 'edit'
            || $this->request->getParam('action') === 'delete') {
            if ($project_role == "senior_developer" || $project_role == "supervisor") {
                return true;
            }
            return false;
        }
        // if not trying to add edit delete
        return parent::isAuthorized($user);
    }
}
