<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

class WeeklyreportsController extends AppController
{

    public function initialize(): void {
        parent::initialize();
    }
    
    public function index() {
        // only load weeklyreports from the current project
        // they are in order by year and week
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $this->paginate = [
            'contain' => ['Projects'],
            'conditions' => array('Weeklyreports.project_id' => $project_id),
            'order' => ['year' => 'DESC', 'week' => 'DESC']
        ];
        $this->set('weeklyreports', $this->paginate($this->Weeklyreports));
        $this->set('_serialize', ['weeklyreports']);
    }

    public function view($id = null) {
        $admin = $this->request->getSession()->read('is_admin');
        $supervisor = ( $this->request->getSession()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
     
        // only load if the report is from the current project unless admin/superv.
        $weeklyreport = $this->Weeklyreports->get($id, ['contain' => ['Projects', 'Metrics', 'Workinghours']]);

        // admin/superv. can access without conditions
        if (!($admin || $supervisor)
            && (is_null($this->request->getSession()->read('selected_project'))
            || $this->request->getSession()->read('selected_project')['id'] != $weeklyreport->project_id)) {
            return $this->redirect(['controller' => 'Projects', 'action' => 'index']);
        }

        $project = \Cake\ORM\TableRegistry::get('Projects')->get($weeklyreport->project_id, ['contain' => ['Members', 'Metrics', 'Weeklyreports']]);
        $this->request->getSession()->write('selected_project', $project);
        
        $metricNames = (new MetricsController())->getMetricNames();
            
        foreach ($weeklyreport->metrics as $metrics) {
            $metrics['metric_description'] = $metricNames[$metrics->metrictype_id];
        }
        
        //get the weekly risks of the report
        $risks = array();
        $risksController = new RisksController();
        $riskTypes = $risksController->getSeverityProbTypes();
        $impactTypes = $risksController->getImpactTypes();
        $categories = $risksController->getCategories();
        $statusTypes = $risksController->getStatusTypes();
        $currentWeeklyRisks = TableRegistry::get('Weeklyrisks')->find()->where(['weeklyreport_id' => $weeklyreport['id']]);
        
        foreach ($currentWeeklyRisks as $weeklyRisk) {
            $risk = new \stdClass();
            
            $risk->description = TableRegistry::get('Risks')->get($weeklyRisk['risk_id'])['description'];
            $risk->cause = TableRegistry::get('Risks')->get($weeklyRisk['risk_id'])['cause'];
            $risk->mitigation = TableRegistry::get('Risks')->get($weeklyRisk['risk_id'])['mitigation'];
            $risk->severity = $riskTypes[$weeklyRisk['severity']];
            $risk->probability = $riskTypes[$weeklyRisk['probability']];
            $risk->status = $statusTypes[$weeklyRisk['status']];
            $risk->category = $categories[$weeklyRisk['category']];
            $risk->impact = $impactTypes[$weeklyRisk['impact']];
            $risk->realizations = $weeklyRisk->realizations;
            $risk->date = $weeklyRisk->date;
            $risk->id = $weeklyRisk->id;
            
            $risks[] = $risk;
        }
        
    // comments stuff
        $this->set('risks', $risks);
        $this->set('weeklyreport', $weeklyreport);
        $this->set('_serialize', ['weeklyreport']);
    }

    public function add() {
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $weeklyreport = $this->Weeklyreports->newEntity([]);
        
        if ($this->request->is('post')) {
            // read the form data and edit it
            $report = $this->request->getData();
            $report['project_id'] = $project_id;
            $report['created_on'] = Time::now();
            $report['created_by'] = $this->request->getSession()->read('Auth.User.id');

            // validate the data and apply it to the weeklyreport object
            $weeklyreport = $this->Weeklyreports->patchEntity($weeklyreport, $report);
  
            // if the object validated correctly and it is unique we can save it in the session
            // and move on to the next page
            if ($this->Weeklyreports->checkUnique($weeklyreport)) {
                if ($this->Weeklyreports->checkWhenProjectCreated($weeklyreport)) {
                    if (!$weeklyreport->errors()) {
                        $this->request->getSession()->write('current_weeklyreport', $weeklyreport);
                        return $this->redirect(
                            ['controller' => 'Metrics', 'action' => 'addmultiple']
                        );
                    } else {
                        $this->Flash->error(__('Report failed validation'));
                    }
                } else {
                    $this->Flash->error(__('Check week and/or year.'));
                }
            } else {
                $this->Flash->error(__('This week already has a weeklyreport'));
            }
        }
        $this->set(compact('weeklyreport'));
        $this->set('_serialize', ['weeklyreport']);
    }
    
    public function edit($id = null) {
        // only allow editing id the weeklyreport is from the current project
        $project_id = $this->request->getSession()->read('selected_project')['id'];
        $weeklyreport = $this->Weeklyreports->get($id, [
            'contain' => [],
            'conditions' => array('Weeklyreports.project_id' => $project_id)
        ]);
        
        $old_weeknumber = $weeklyreport['week'];
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $weeklyreport = $this->Weeklyreports->patchEntity($weeklyreport, $this->request->getData());
            // project id cannot be changed, and its made sure it does not change
            $weeklyreport['project_id'] = $project_id;
            // updated_on date is automatic
            $weeklyreport['updated_on'] = Time::now();
            $weeklyreport['updated_by'] = $this->request->getSession()->read('Auth.User.id');
            
            // check that this week does not already have a weeklyreport.
            // but allow updating withouth changing the week number
            // checkUnique is in "WeeklyreportsTable.php"
            if ($this->Weeklyreports->checkUnique($weeklyreport) || $old_weeknumber == $weeklyreport['week']) {
                if ($this->Weeklyreports->checkWhenProjectCreated($weeklyreport)) {
                    if ($this->Weeklyreports->save($weeklyreport)) {
                        $this->Flash->success(__('The weeklyreport has been saved.'));
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $this->Flash->error(__('The weeklyreport could not be saved. Please, try again.'));
                    }
                } else {
                    $this->Flash->error(__('Check week and/or year.'));
                }
            } else {
                $this->Flash->error(__('This week already has a weeklyreport'));
            }
        }
        $this->set(compact('weeklyreport'));
        $this->set('_serialize', ['weeklyreport']);
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $weeklyreport = $this->Weeklyreports->get($id);

        if ($this->Weeklyreports->delete($weeklyreport)) {
            $this->Flash->success(__('The weeklyreport has been deleted.'));
        } else {
            $this->Flash->error(__('The weeklyreport could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function preparemail($id = null) {
        $content = '<div style="max-width:600px">';
        
        $weeklyreport = $this->Weeklyreports->get($id, [
            'contain' => ['Projects', 'Metrics', 'Workinghours']
        ]);
        
        $projectId = $weeklyreport->project_id;
    
        $tableStyle = 'border="1" style="width:100%;border-collapse:collapse;margin-bottom:20px"';
        $titleStyle = 'colspan="3" style="text-align:center;font-size:18px;padding:5px"';
        
        $project = '<table '.$tableStyle.'>';
        
        $rightAlign = 'style="text-align:right"';
        
        $project .= '<tr><td>Title</td><td '.$rightAlign.'>'.$weeklyreport->title.'</td></tr>';
        $project .= '<tr><td>Week</td><td '.$rightAlign.'>'.$weeklyreport->week.'</td></tr>';
        $project .= '<tr><td>Year</td><td '.$rightAlign.'>'.$weeklyreport->year.'</td></tr>';
        $project .= '<tr><td>Meetings</td><td '.$rightAlign.'>'.$weeklyreport->meetings.'</td></tr>';
        $project .= '<tr><td>Realized risks,Challenges, issues, etc.</td><td '.$rightAlign.'>'.$weeklyreport->problems.'</td></tr>';
        $project .= '<tr><td>Addtitional</td><td '.$rightAlign.'>'.$weeklyreport->additional.'</td></tr>';
        $project .= '<tr><td>Created on</td><td '.$rightAlign.'>'.$weeklyreport->created_on->format('d.m.Y').'</td></tr>';
        $project .= '<tr><td>Updated on</td><td '.$rightAlign.'>'.($weeklyreport->updated_on != null ? $weeklyreport->updated_on->format('d.m.Y') : '').'</td></tr>';
        $project .= '</table>';

        $content .= $project;
        
        $reporthours = '<table '.$tableStyle.'>';
        $reporthours .= '<tr><td '.$titleStyle.'>Working Hours for week '.$weeklyreport->week.'</td></tr>';
        $reporthours .= '<tr><td>Name</td><td>Project Role</td><td>Working hours</td></tr>';
        
        $membersList = TableRegistry::get('Members')->find('all', [
            'conditions' => ['project_id' => $projectId],
            'contain' => ['Users', 'Projects', 'Workinghours']
        ])->toArray();
            
            
        foreach ($membersList as $member) {
            if ($member->project_role === 'senior_developer' || $member->project_role === 'developer') {
                $name = $member->user->full_name;
                $hours = 0;
                $queryForHours = $member->workinghours;
                    
                foreach ($queryForHours as $key) {
                    if ($weeklyreport->week == $key->date->format('W')) {
                        if (($weeklyreport->week == 52 && $key->date->format('m') == 01) ||
                            ($weeklyreport->week == 5 && $key->date->format('m') == 01) ||
                            ($weeklyreport->week == 1 && $key->date->format('m') == 12) ||
                            ($weeklyreport->year == $key->date->format('Y'))) {
                            $hours += $key->duration;
                        }
                    }
                }

                $reporthours .= '<tr><td>'.$name.'</td><td>'.$member->project_role.'</td><td>'.$hours.'</td></tr>';
            }
        }
        
        $reporthours .= '</table>';
        $content .= $reporthours;
        
        $metricList = '<table '.$tableStyle.'>';
        $metricList .= '<tr><td '.$titleStyle.'>Metrics</td></tr>';
        $metricList .= '<tr><td>Metric type</td><td>Value</td><td>Date</td></tr>';
        $metricNames = (new MetricsController())->getMetricNames();
            
        foreach ($weeklyreport->metrics as $metrics) {
            $metricList .= '<tr><td>'.$metricNames[$metrics->metrictype_id].'</td><td>'.$metrics->value.'</td><td>'.$metrics->date->format('d.m.Y').'</td></tr>';
        }
        
        $metricList .= '</table>';
        $content .= $metricList;
        
        $currentWeeklyRisks = TableRegistry::get('Weeklyrisks')->find()->where(['weeklyreport_id' => $weeklyreport['id']])->toArray();
        
        if (!empty($currentWeeklyRisks)) {
            $risks = '<table '.$tableStyle.'>';
            $risks .= '<tr><td '.$titleStyle.'>Risks</td></tr>';
            $risks .= '<tr><td>Risk</td><td>Severity</td><td>Probability</td></tr>';

            $riskTypes = (new RisksController())->getSeverityProbTypes();

            foreach ($currentWeeklyRisks as $weeklyRisk) {
                $riskdescription = TableRegistry::get('Risks')->get($weeklyRisk['risk_id'])['description'];
                $riskseverity = $riskTypes[$weeklyRisk['severity']];
                $riskprobability = $riskTypes[$weeklyRisk['probability']];

                $risks .= '<tr><td>'.$riskdescription.'</td><td>'.$riskseverity.'</td><td>'.$riskprobability.'</td></tr>';
            }
            
            $risks .= '</table>';
            $content .= $risks;
        }
        
        $content .= '</div>';
    
        return $content;
    }
    
    public function isAuthorized($user) {

        if ($user['role'] === 'inactive') {
            $this->Auth->setConfig('authError', false);
            $this->Flash->error(__('Inactive users are not authorized to access this page.'));
            return false;
        }

        if ($this->request->getParam('action') === 'preparemail') {
            return true;
        }
       
        return parent::isAuthorized($user);
    }
}
