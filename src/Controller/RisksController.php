<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;

class RisksController extends AppController
{

    public function index() {
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $deletable = array();
        $risks = $this->Risks->find()->where(['project_id' => $project_id]);
        $types = $this->getSeverityProbTypes();
        $impactTypes = $this->getImpactTypes();
        $statusTypes = $this->getStatusTypes();
        $categories = $this->getCategories();
        
        foreach ($risks as $risk) {
            $deletable[$risk->id] = $this->checkDeletable($risk->id);
        }
        
        $this->set(compact('risks', 'types', 'categories', 'impactTypes', 'statusTypes', 'deletable'));
    }
    
    public function add() {
        $risk = $this->Risks->newEntity([]);
        
        if ($this->request->is('post')) {
            // get data from the form
            $risk = $this->Risks->patchEntity($risk, $this->request->getData());
            $risk['project_id'] = $this->request->getSession()->read('selected_project')['id'];
            
            if ($this->Risks->save($risk)) {
                $this->Flash->success(__('The risk has been added.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The risk could not be added. Please, try again.'));
            }
        }
        
        $types = $this->getSeverityProbTypes();
        $impactTypes = $this->getImpactTypes();
        $statusTypes = $this->getStatusTypes();
        $categories = $this->getCategories();
        
        $this->set(compact('risk', 'types', 'categories', 'impactTypes', 'statusTypes'));
        $this->set('_serialize', ['risk']);
    }
    
    public function delete($id = null) {
        $risk = $this->Risks->get($id);
        
        if ($this->checkDeletable($id)) {
            if ($this->Risks->delete($risk)) {
                $this->Flash->success(__('The risk has been deleted.'));
            } else {
                $this->Flash->error(__('The risk could not be deleted. Please, try again.'));
            }
        } else {
            $this->Flash->error(__('This risk is already contained in a weekly report, and thus can not be deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function edit($id = null) {
        $risk = $this->Risks->get($id);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $risk = $this->Risks->patchEntity($risk, $this->request->getData());

            if ($this->Risks->save($risk)) {
                $this->Flash->success(__('The risk has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The risk could not be saved. Please, try again.'));
            }
        }
        
        $deletable = $this->checkDeletable($id);
        $types = $this->getSeverityProbTypes();
        $impactTypes = $this->getImpactTypes();
        $statusTypes = $this->getStatusTypes();
        $categories = $this->getCategories();

        $this->set(compact('risk', 'types', 'categories', 'impactTypes', 'statusTypes', 'deletable'));
        $this->set('_serialize', ['risk']);
    }
    
    public function addweekly() {
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $risks = $this->Risks->find()->where(['project_id' => $project_id])->toArray();
        $types = $this->getSeverityProbTypes();
        $impactTypes = $this->getImpactTypes();
        $statusTypes = $this->getStatusTypes();
        $categories = $this->getCategories();
        
        if ($this->request->is('post')) {
            $temp = array();
            $data = $this->request->getData();
            
            //Puts each risk value to the array
            foreach ($data as $key => $value) {
                if (strpos($key, 'category') !== false) {
                    $riskId = str_replace('category-', '', $key);
                    $temp[$riskId]['category'] = $value;
                }

                if (strpos($key, 'prob') !== false) {
                    $riskId = str_replace('prob-', '', $key);
                    $temp[$riskId]['probability'] = $value;
                }
                
                if (strpos($key, 'severity') !== false) {
                    $riskId = str_replace('severity-', '', $key);
                    $temp[$riskId]['severity'] = $value;
                    
                }

                if (strpos($key, 'impact') !== false) {
                    $riskId = str_replace('impact-', '', $key);
                    $temp[$riskId]['impact'] = $value;
                    
                }

                if (strpos($key, 'category') !== false) {
                    $riskId = str_replace('category-', '', $key);
                    $temp[$riskId]['category'] = $value;
            
                }

                if (strpos($key, 'status') !== false) {
                    $riskId = str_replace('status-', '', $key);
                    $temp[$riskId]['status'] = $value;
                    
                }



                if (strpos($key, 'real') !== false && $value != null) {
                    $riskId = str_replace('real-', '', $key);
                    $temp[$riskId]['realizations'] = $value;
                    $realizations = $value;

                    $risk = $this->Risks->get($riskId);
                    $risk->realizations = $risk->realizations + intval($realizations);

                    if ($this->Risks->save($risk)) {
                        $this->Flash->success(__(''));
                    } else {
                        $this->Flash->error(__('Unable to update risk realization count.'));
                    }
                }
            }

            //Writes the selected values to the session
            $this->request->getSession()->write('current_risks', $temp);
            
            return $this->redirect(['controller' => 'Weeklyhours', 'action' => 'addmultiple']);
        }
        
        $current_risks = $this->getLatestRisks($project_id);
        $this->set(compact('risks', 'types', 'categories', 'current_risks','impactTypes', 'statusTypes'));
    }

    public function getCategories() {
        $categories = array();

        $categories[0] = 'Uncategorized';
        $categories[1] = 'Political';
        $categories[2] = 'Economic';
        $categories[3] = 'Social';
        $categories[4] = 'Technological';
        $categories[5] = 'Environmental';
        $categories[6] = 'Legal';
        
        return $categories;
    }

    public function getSeverityProbTypes() {
        $types = array();
        
        $types[0] = 'None';
        $types[1] = 'Very Low';
        $types[2] = 'Low';
        $types[3] = 'Medium';
        $types[4] = 'High';
        $types[5] = 'Very High';
        
        return $types;
    }

    public function getImpactTypes() {
        $impactTypes = array();

        $impactTypes[0] = 'Budget';
        $impactTypes[1] = 'Time';
        $impactTypes[2] = 'Scope';
        $impactTypes[3] = 'Benefit';

        return $impactTypes;
    }
    
    public function getStatusTypes() {
        $statusTypes = array();

        $statusTypes[0] = 'Active';
        $statusTypes[1] = 'Mitigated';
        $statusTypes[2] = 'Closed';

        return $statusTypes;
    }
    public function checkDeletable($riskId) {
        $weekly = TableRegistry::get('Weeklyrisks')->find()->where(['risk_id' => $riskId])->toArray();
        
        return empty($weekly);
    }
    
    //This is for getting the latest probability values for project risks
    public function getLatestRisks($projectId) {
        $latestRisks = array();
        $risks = $this->Risks->find()->where(['project_id' => $projectId]);
        
        foreach ($risks as $risk) {
            //Looks for the latest value for this risk
            $weeklyRisk = TableRegistry::get('Weeklyrisks')->find('all', [
                'conditions' => ['risk_id' => $risk['id']],
                'order' => ['weeklyreport_id' => 'DESC']
            ])->first();
            
            if ($weeklyRisk !== null) {
                $item = array();
                $item['category'] = $weeklyRisk['category'];
                $item['probability'] = $weeklyRisk['probability'];
                $item['severity'] = $weeklyRisk['severity'];
                $item['impact'] = $weeklyRisk['impact'];
                $item['status'] = $weeklyRisk['status'];
                
                $latestRisks[$risk['id']] = $item;
            } else {
                //If no previous weekly risk is found, it will get the starting severity and probability value
                $item = array();
                $item['category'] = $risk['category'];
                $item['probability'] = $risk['probability'];
                $item['severity'] = $risk['severity'];
                $item['impact'] = $risk['impact'];
                $item['status'] = $risk['status'];
                
                $latestRisks[$risk['id']] = $item;
            }
        }
        
        return $latestRisks;
    }
    
    public function isAuthorized($user) {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        $project_role = $this->request->getSession()->read('selected_project_role');
        
        if ($this->request->getParam('action') === 'addweekly') {
            //Only senior developer and admin can add risks to the report
            return ($project_role == 'senior_developer' || $project_role == 'admin');
        } else if (in_array($this->request->getParam('action'), ['add','edit','delete'])) {
            //Only senior developer and admin can add new risk
            $allow = ($project_role == 'senior_developer' || $project_role == 'admin');
            
            if ($allow && ($this->request->getParam('action') === 'edit' || $this->request->getParam('action') === 'delete')) {
                $riskId = $this->request->getParam('pass')[0];
                $projectId = $this->Risks->get($riskId)->project_id;
                //One can only edit or delete risks for the current selected project
                $allow = $projectId == $this->request->getSession()->read('selected_project')['id'];
            }
            return $allow;
        } else {
            return true;
        }
    }
}
