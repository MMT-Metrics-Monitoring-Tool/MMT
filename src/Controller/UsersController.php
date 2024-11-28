<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Error\Debugger;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;

class UsersController extends AppController
{

    private static $PASS_MIN_LENGTH = 8;

    public function index() {
        $this->paginate = [
            'limit' => 100,
            'order' => ['last_name' => 'ASC']
        ];

        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }
    
    public function login() {
        if ($this->Auth->user('id') != null) {
            return $this->redirect(
                ['controller' => 'Projects', 'action' => 'index']
            );
        }

        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                
                // if user is inactive, redirect to login page and show error message
                /** 
                * if ($user['role'] === 'inactive') {
                *     $this->Flash->error('Your account is inactive. Please contact an administrator.');
                *     return;
                *}
                */
                $this->Auth->setUser($user);
                // this is used so that normal user can be directed straight to workinghours after login
                $this->request->getSession()->write('first_view', true);
                return $this->redirect(
                    ['controller' => 'Projects', 'action' => 'index']
                );
            }
            $this->Flash->error('Your username or password is incorrect.');
        }
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

        return $this->redirect($this->Auth->logout());
    }
    
    public function view($id = null) {
        $user = $this->Users->get($id, [
            'contain' => ['Members']
        ]);

        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    public function add() {
        $user = $this->Users->newEntity([]);

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
    
    public function signup() {
        $user = $this->Users->newEntity([]);

        if ($this->request->is('post')) {
            /*
             * CHANGE THE VALUE HERE
             * Check if the user is human
             */
            if ($this->request->getData()['checkIfHuman'] == 5) {
                // when adding a new user, make the role always "user", as in normal user
                $user = $this->Users->patchEntity($user, $this->request->withData('role', 'user')->getData());
                
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('Your account has been saved.'));
                    return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                } else {
                    $errors = $user->getErrors();
                    if (isset($errors['email']['unique'])) {
                        $this->Flash->error(__('This Email is already in use'));
                    } elseif (isset($errors['password']['length'])) {
                        $this->Flash->error(__('The password has to be at least 8 characters long'));
                    } else {
                        foreach ($errors as $field => $msgs) {
                            foreach ($msgs as $msg) {
                                $this->Flash->error(__($field . ': ' . $msg));
                            }
                        }
                    }
                }
            } else {
                    $this->Flash->error(__('Check the sum.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    public function edit($id = null) {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            // If user is being set to inactive, set working hours target on all active projects to current working hours
            if ($this->request->getData()['role'] == 'inactive') {
                $this->Users->setUserTargetWorkingHoursToCurrentWorkingHours($user['id']);
            }

            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
    
    public function editprofile() {
        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The profile has been updated.'));
                return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
    
    // Image upload functionality works, but there is a problem with permissions on the server. So this commented for now.
//    public function photo()
//    {
//        if ($this->request->is(['patch', 'post', 'put'])) {
//
//            $action = $this->request->getData()['action'];
//
//            if($action === 'upload'){
//
//                $imageFile = $this->request->getData()['image'];
//
//                if($imageFile['size'] === 0){
//                    $this->Flash->error(__('File not found.'));
//                }else{
//
//                    $check = getimagesize($imageFile['tmp_name']);
//
//                    if(!$check){
//                        $this->Flash->error(__('File is not an image.'));
//                    }else{
//
//                        $userId = $this->Auth->user('id');
//
//                        $targetPath = WWW_ROOT . 'img' . DS . 'profile' . DS . 'user_' . $userId . '.png';
//
//                        if (move_uploaded_file($imageFile["tmp_name"], $targetPath)) {
//
//                            $this->Flash->success(__('The image has been uploaded.'));
//                        } else {
//
//                            $this->Flash->error(__('The image can not be uploaded. Please, try again.'));
//                        }
//                    }
//                }
//            }else if ($action === 'delete'){
//
//                $userId = $this->Auth->user('id');
//
//                $path = WWW_ROOT . 'img' . DS . 'profile' . DS . 'user_' . $userId . '.png';
//
//                if(unlink($path)){
//                    $this->Flash->success(__('The image has been deleted.'));
//                }else{
//                    $this->Flash->error(__('The image be deleted. Please, try again.'));
//                }
//
//            }
//
//        }
//    }
    
    public function password() {
        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->request->getData()['password'] == $this->request->getData()['checkPassword']) {
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('The profile has been updated.'));
                    return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
                } else {
                    $this->Flash->error(__('The user could not be saved. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('Passwords are not a match. Try again, please.'));
            }
        }

        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }
    
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function forgotpassword() {
        if ($this->request->is('post')) {
            $email = $this->request->getData()['email'];
            $checkUser = $this->Users->find()->where(['email' => $email])->toArray();
            
            if (empty($checkUser)) {
                $this->Flash->error(__('This email does not belong to any user.'));
            } else {
                $user = $checkUser[0];
                $key = $string = substr(md5(rand()), 0, 25);
                $user->password_key = $key;
                
                if ($this->Users->save($user)) {
                    $sendMail = new Email();
                    $sendMail->setFrom(['mmt@uta.fi' => 'MMT']);
                    $sendMail->setTo($email);
                    $sendMail->setSubject('Password reset');
                    $sendMail->setEmailFormat('html');
                    
                    if ($sendMail->send($this->prepareEmail($key))) {
                        $this->Flash->success(__('Key for reseting your password has been sent to your email.'));
                        
                        return $this->redirect(['controller' => 'Users','action' => 'login']);
                    } else {
                        $this->Flash->error(__('An error occured when sending the email, please try again.'));
                    }
                } else {
                    $this->Flash->error(__('An error occured when sending the email, please try again.'));
                }
            }
        }
    }
    
    public function resetpassword($key = null) {
        $showForm = false;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $showForm = true;
            $user = $this->Users->find()->where(['password_key' => $key]);
            
            if ($user->first() === null) {
                $this->Flash->error(__('Invalid key.'));
            } else {
                $user = $user->first();
                
                if ($this->request->getData()['password'] == $this->request->getData()['checkPassword']) {
                    if (strlen($this->request->getData()['password']) < 8) {
                        $this->Flash->error(__('The password has to be 8 characters long'));
                    } else {
                        $user->password = $this->request->getData()['password'];
                        $user->password_key = null;
                    
                        if ($this->Users->save($user)) {
                            $this->Flash->success(__('Your password has been updated.'));
                            $showForm = false;
                
                            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                        } else {
                            $this->Flash->error(__('The user could not be saved. Please, try again.'));
                        }
                    }
                } else {
                    $this->Flash->error(__('Passwords are not a match. Try again, please.'));
                    $showForm = true;
                }
            }
        } else {
            if ($key == null) {
                $this->Flash->error(__('Invalid key.'));
            } else {
                $getUser = $this->Users->find()->where(['password_key' => $key]);

                if ($getUser->first() === null) {
                    $this->Flash->error(__('Invalid key.'));
                } else {
                    $user = $getUser->first();
                    $showForm = true;
                }
            }
        }
        $this->set(compact('showForm', 'key'));
    }
    
    // this allows anyone to go and create users, or reset forgotten password without logging in
    public function beforeFilter(\Cake\Event\EventInterface $event) {
        $this->Auth->allow(['signup']);
        $this->Auth->allow(['forgotpassword']);
        $this->Auth->allow(['resetpassword']);
    }
    
    public function isAuthorized($user) {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        if ($this->request->getParam('action') === 'add' || $this->request->getParam('action') === 'edit'
            || $this->request->getParam('action') === 'delete' || $this->request->getParam('action') === 'index') {
            return false;
        }

        if ($this->request->getParam('action') === 'view') {
            $id_length = ceil(log10(abs($user['id']) + 1));

            if ($user['id'] == substr($this->request->getUri(), -$id_length)) {
                return true;
            } else {
                return false;
            }
        }
        
        // All registered users can edit their own profile and logout
        if ($this->request->getParam('action') === 'logout' || $this->request->getParam('action') === 'editprofile'
            || $this->request->getParam('action') === 'password' || $this->request->getParam('action') === 'photo') {
            return true;
        }
        
        return parent::isAuthorized($user);
    }
    
    public function prepareEmail($key) {
        $url = Router::url(['controller' => 'Users','action' => 'resetpassword',$key], true);
        $message = '<p>In order to reset your password, visit this link:</p>';
        $message .= '<p><a href="'.$url.'">'.$url.'</a></p>';
        
        return $message;
    }
}
