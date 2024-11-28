<?php
namespace App\Model\Table;

use App\Model\Entity\Weeklyhour;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class WeeklyhoursTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('weeklyhours');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Weeklyreports', [
            'foreignKey' => 'weeklyreport_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER'
        ]);
    }
    
    // check if the weeklyreport and member_id pair already exists
    public function checkUnique($hour) {
        $weeklyhours = TableRegistry::get('Weeklyhours');
        $query = $weeklyhours
                ->find()
                ->select(['weeklyreport_id', 'member_id'])
                ->where(['weeklyreport_id =' => $hour['weeklyreport_id']])
                ->where(['member_id =' => $hour['member_id']]);
                
        foreach ($query as $temp) {
            if ($temp['weeklyreport_id'] == $hour['weeklyreport_id']) {
                return false;
            }
        }

        return true;
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');

        $validator
            ->add('duration', 'valid', [
                'rule' => 'numeric',
                // minimum of 0 hours, max of 7 * 24
                'rule' => ['range', 0, 168]
                ])
            ->requirePresence('duration', 'create')
            ->notEmptyString('duration');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        $rules->add($rules->existsIn(['weeklyreport_id'], 'Weeklyreports'));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        $rules->add($rules->isUnique(['member_id', 'weeklyreport_id']));

        return $rules;
    }
    
    public function formatData($formdata, $current_weeklyreport) {
        // keys from the form, for going trough the key value pairs
        $keys = array_keys($formdata);
        $weeklyhours = array();

        // in this for loop we format the data correctly and insert the weeklyreport_id
        for ($count = 0; $count < count($formdata); $count++) {
            $temp = $formdata[$keys[$count]];
            $temp['weeklyreport_id'] = $current_weeklyreport['id'];
            $weeklyhours[] = $temp;
        }

        return $weeklyhours;
    }
    
    public function duplicates($weeklyhours) {
        $tempmembers = array();

        foreach ($weeklyhours as $temp) {
            // keep a list of all members to and make sure there is only one of each
            if (in_array($temp['member_id'], $tempmembers)) {
                return true;
            } else {
                $tempmembers[] = $temp['member_id'];
            }
        }

        return false;
    }
    
    // count the ammount of workinghours for the members in the week
    public function getHours($memberlist, $week) {
        $workinghours = TableRegistry::get('Workinghours');
        $hours = array();

        foreach ($memberlist as $member) {
            $temphour = 0;
            $query = $workinghours
               ->find()
               ->select(['duration', 'date'])
               ->where(['member_id =' => $member['id']])
               ->toArray();
            
            foreach ($query as $temp) {
                $time = new Time($temp['date']);

                if ($time->weekOfYear == $week) {
                    $temphour = $temphour + $temp['duration'];
                }
            }
            $hours[] = $temphour;
        }

        return $hours;
    }
    
    // saving the weeklyreport created with the weeklyreportform
    // this also saves the metrics and weeklyhours that belong to the weeklyreport
    public function saveSessionReport($weeklyreport, $metrics, $risks, $weeklyhours) {
        $tableWeeklyreports = TableRegistry::get('Weeklyreports');
        // saving the actual weeklyreport
        if (!$tableWeeklyreports->save($weeklyreport)) {
            return false;
        }
        // saving the weeklyreport created an id for the weeklyreport
        // the id will now be added to the metrics and weeklyhours
        $tableMetrics = TableRegistry::get('Metrics');
        
        foreach ($metrics as $temp) {
            $temp['weeklyreport_id'] = $weeklyreport['id'];

            if (!$tableMetrics->save($temp)) {
                return false;
            }
        }
        
        //saving the weekly risks
        $tableWeeklyrisks = TableRegistry::get('Weeklyrisks');
        $time = Time::now();

        foreach ($risks as $id => $prob) {
            $weeklyRisk = $tableWeeklyrisks->newEntity([]);
            
            $weeklyRisk['weeklyreport_id'] = $weeklyreport['id'];
            $weeklyRisk['risk_id'] = $id;
            $weeklyRisk['probability'] = $prob['probability'];
            $weeklyRisk['severity'] = $prob['severity'];
            $weeklyRisk['date'] = $time;
            $weeklyRisk['category'] = $prob['category'];
            $weeklyRisk['status'] = $prob['status'];
            $weeklyRisk['impact'] = $prob['impact'];
            $weeklyRisk['realizations'] = intval($prob['realizations']);
            
            if (!$tableWeeklyrisks->save($weeklyRisk)) {
                return false;
            }
        }
        
        $tableWeeklyhours = TableRegistry::get('Weeklyhours');

        foreach ($weeklyhours as $temp) {
            $temp['weeklyreport_id'] = $weeklyreport['id'];

            if (!$tableWeeklyhours->save($temp)) {
                return false;
            }
        }

        return true;
    }
}
