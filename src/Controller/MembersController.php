<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class MembersController extends AppController
{
    public function initialize(): void {
        parent::initialize();

        // Argument 1 passed to Cake\Http\Session::_overwrite() must be of the type array, null given,
        // called in /var/www/html/vendor/cakephp/cakephp/src/Http/Session.php on line 495
        $this->request->getSession()->read();
    }

    public function index() {
        // Only members of the current project are loaded
        $project_id = $this->request->getSession()->read('selected_project')['id'];

        
        $this->paginate = [
            //'contain' => ['Users', 'Projects', 'Workinghours', 'Weeklyhours'],
            'contain' => ['Users', 'Projects', 'Workinghours'],
            'conditions' => array('Members.project_id' => $project_id)
        ];

        $this->set('members', $this->paginate($this->Members));
        $this->set('_serialize', ['members']);

        // Chart for project's workinghour prediction
        // Find and store info that will be needed by chart data function in MembersTable
        $projectStartDate = clone $this->request->getSession()->read('selected_project')['created_on'];
        $endingDate = $this->request->getSession()->read('selected_project')['finished_date'];
        
        $predictiveProjectData = $this->Members->predictiveProjectData($project_id, $projectStartDate, $endingDate);
        $this->request->getSession()->write('predictiveProjectData', $predictiveProjectData);

        $this->set('hoursByTypeData', $this->Members->hoursByTypeData($project_id));
    }

    public function view($id = null) {
        // The member with the id "$id" is loaded
        // IF the member is a part of the currently selected project
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $member = $this->Members->get($id, [
            'contain' => ['Users', 'Projects', 'Workinghours', 'Weeklyhours'],
            'conditions' => array('Members.project_id' => $project_id)
        ]);

        $this->set('member', $member);
        $this->set('_serialize', ['member']);

        // Chart for workinghour prediction
        // Find and store info that will be needed by chart data function in MembersTable
        $projectStartDate = clone $this->request->getSession()->read('selected_project')['created_on'];
        $endingDate = $this->request->getSession()->read('selected_project')['finished_date'];
        $predictiveMemberData = $this->Members->predictiveMemberData($project_id, $member['id'], $projectStartDate, $endingDate);
        $this->request->getSession()->write('predictiveMemberData', $predictiveMemberData);
    }

    public function add() {
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $member = $this->Members->newEntity([]);
        
        if ($this->request->is('post')) {
            // data from the form is loaded in to the new member object
            $member = $this->Members->patchEntity($member, $this->request->getData());
            // the member is made a part of the currently selected project
            $member['project_id'] = $project_id;
            $email = $this->request->getData()['email'];

            $query = TableRegistry::get('Users')
                ->find()
                ->select(['id'])
                ->where(['email =' => $email])
                ->toArray();

            $id = null;
            foreach ($query as $temp) {
                $id = $temp['id'];
            }

            if (is_null($id)) {
                $this->Flash->error(__('Member does not exist'));
                $this->response = $this->response->withStatus(404);
            } else {
                //Get matching user id's from current project
                $memberQuery = TableRegistry::get('Members')
                    ->find()
                    ->select('user_id')
                    ->where(['user_id =' => $id, 'project_id =' => $project_id])
                    ->toArray();

                //If ID doesn't exist in project, proceed
                if (sizeof($memberQuery) == 0) {
                    $member['user_id'] = $id;

                    // Senior developers are not allowed to add members that are supervisors
                    if ($member['project_role'] != "supervisor" || $this->request->getSession()->read('selected_project_role') != 'senior_developer') {
                        if ($this->Members->save($member)) {
                            $this->Flash->success(__('The member has been saved.'));
                            return $this->redirect(['action' => 'index']);
                        } else {
                            $this->Flash->error(__('The member could not be saved. Please, try again.'));
                        }
                    } else {
                        $this->Flash->error(__('Senior developers cannot add supervisors'));
                    }
                } else {
                    $this->Flash->error(__('The member is already part of the project.'));
                }
            }
        }
        $users = $this->Members->Users->find('list', ['limit' => 1000, 'conditions'=>array('Users.role !=' => 'inactive')]);
        $this->set(compact('member', 'users'));
        $this->set('_serialize', ['member']);
    }

    public function edit($id = null) {
        $project_id = $this->request->getSession()->read('selected_project')['id'];

        // The selected member is only loaded if the member is a part of the curren project
        $member = $this->Members->get($id, [
            'contain' => [],
            'conditions' => array('Members.project_id' => $project_id)
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            // data is loaded from the form
            $member = $this->Members->patchEntity($member, $this->request->getData());
            // it is made sure that the updated member stays in the current project
            $member['project_id'] = $project_id;

            if ($this->Members->save($member)) {
                $this->Flash->success(__('The member has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The member could not be saved. Please, try again.'));
            }
        }

        $users = $this->Members->Users->find('list', ['limit' => 200, 'conditions'=>array('Users.role !=' => 'inactive')]);
        $this->set(compact('member'));
        $this->set('_serialize', ['member']);
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $member = $this->Members->get($id);

        if ($this->Members->delete($member)) {
            $this->Flash->success(__('The member has been deleted.'));
        } else {
            $this->Flash->error(__('The member could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function anonymizeAll() {
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $modifiedMemberlist = $this->Members->anonymizeAllMembers($project_id);

        foreach ($modifiedMemberlist as $anonymizedMember) {
            $this->Members->save($anonymizedMember);
        }

        return $this->redirect(['action' => 'index']);
    }
       
    public function isAuthorized($user) {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        // Allow inactive users to view member data
        if (isset($user['role']) && $user['role'] === 'inactive') {
            if ($this->request->getParam('action') === 'index') {
                return true;
            }
            if ($this->request->getParam('action') === 'view') {
                $memberId = $this->request->getParam('pass')[0];

                $member = $this->Members->get($memberId, [
                    'contain' => ['Users']
                ]);
                return ($member->user_id === $user['id']);
            }
            return false;
        }
        
        $project_role = $this->request->getSession()->read('selected_project_role');
        
        // special rules for members controller.
        // senior developers can add members, but cannot add new supervisors
        if ($this->request->getParam('action') === 'add') {
            if ($project_role == "senior_developer" || $project_role == "supervisor") {
                return true;
            }
        }
        // only supervisors and admins can delete members
        if ($this->request->getParam('action') === 'delete') {
            if ($project_role == "supervisor") {
                return true;
            }
            // This return false is important, because if we didnt have it a senior developer could also
            // add edit and delete members. This is because after this if block we call the parent
            return false;
        }

        // in addition to supervisors, senior_developers and admins, member can also edit own data
        if ($this->request->getParam('action') === 'edit') {
            $id_length = ceil(log10(abs($this->request->getSession()->read('selected_project_memberid') + 1)));

            // Allow supervisors and senior developers to edit any member's data
            if ($project_role == "supervisor" || $project_role == "senior_developer") {
                return true;
            }

            // Allow members to edit their own data
            if ($this->request->getSession()->read('selected_project_memberid') == substr($this->request->getUri(), -$id_length)) {
                return true;
            }

            // This return false is important, because if we didnt have it a senior developer could also
            // add edit and delete members. This is because after this if block we call the parent
            return false;
        }
        // if not trying to add edit delete
        return parent::isAuthorized($user);
    }
}
