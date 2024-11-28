<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Controller\WeeklyreportsController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\I18n\Time;

class WeeklyrisksController extends AppController
{

    public function edit($id = null) {
        $risk = $this->Weeklyrisks->get($id);
        $this->request->getSession()->write('selected_risk_description', TableRegistry::get('Risks')->get($risk->risk_id)['description']);
        $wr_id = $risk->weeklyreport_id;
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $risk = $this->Weeklyrisks->patchEntity($risk, $this->request->getData());
            $time = Time::now();
            $risk['date'] = $time;

            if ($this->Weeklyrisks->save($risk)) {
                $this->Flash->success(__('The risk has been saved.'));
                (new WeeklyreportsController())->edit($wr_id);
                return $this->redirect(['controller' => 'weeklyreports', 'action' => 'view', $wr_id]);
            } else {
                $this->Flash->error(__('The risk could not be saved. Please, try again.'));
            }
        }
        
        $risksController = new RisksController();
        $types = $risksController->getSeverityProbTypes();
        
        
        // 'editable' is commented out because it is not needed for this controller's edit.php view
        // and causes an undefined variable warning.
        
        $this->set(compact('risk', 'types'/*, 'editable' */));
        $this->set('_serialize', ['risk']);
    }

    public function export() {
        $this->response = $this->response->withDownload('risks_export.csv');
        $data = $this->Weeklyrisks->find('all', ['contain' => ['Weeklyreports']])->toArray();
        foreach ($data as $value) {
            $risk = TableRegistry::get('Risks')->get($value->risk_id, ['contain' => ['Projects']]);
            $value->project_name = $risk['project']['project_name'];
            $value->description = $risk['description'];
            $value->week = $value['weeklyreport']['week'];
            $value->year = $value['weeklyreport']['year'];
        }
        $_header = ['project_name', 'description', 'probability', 'severity', 'week', 'year'];
        $_extract = ['project_name', 'description', 'probability', 'severity', 'week', 'year'];
        $_serialize = 'data';

        $this->viewBuilder()->setClassName('CsvView.Csv');
        $this->set(compact('data', '_serialize', '_header', '_extract'));
    }
}
