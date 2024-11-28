<?php
namespace App\Model\Table;

use App\Model\Entity\Weeklyreport;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

use Cake\Filesystem\File;
use Cake\I18n\Time;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;

class WeeklyreportsTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('weeklyreports');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Metrics', [
            'foreignKey' => 'weeklyreport_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Workinghours', [
            'foreignKey' => 'member_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
        $this->hasMany('Weeklyhours', [
            'foreignKey' => 'weeklyreport_id',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'weeklyreport_id',
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);
    }
    
    // check if the project_id and week pair already exists
    public function checkUnique($report) {
        $weeklyreports = TableRegistry::get('Weeklyreports');
        $query = $weeklyreports
                ->find()
                ->select(['project_id', 'week'])
                ->where(['project_id =' => $report['project_id']])
                ->where(['year =' => $report['year']])
                ->where(['week =' => $report['week']]);
                
        foreach ($query as $temp) {
            if ($temp['project_id'] == $report['project_id']) {
                return false;
            }
        }

        return true;
    }

    public function checkWhenProjectCreated($report) {
        $projects = TableRegistry::get('Projects');
        $query = $projects
                ->find()
                ->select(['created_on'])
                ->where(['id =' => $report['project_id']]);

        foreach ($query as $result) {
            $temp = date_parse($result);
            $minYear = $temp['year'];
            $month = $temp['month'];
            $day = $temp['day'];

            $minWeek = date("W", mktime(0, 0, 0, $month, $day, $minYear));
        }
        
        if ($report['year'] > $minYear) {
            return true;
        } elseif ($report['year'] == $minYear) {
            if ($report['week'] >= $minWeek) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->add('week', 'valid', [
                'rule' => 'numeric',
                // this is the weeknumber range
                // maximum weeknumber is 53
                'rule' => ['range', 1, 53]
                ])
            ->requirePresence('week', 'create')
            ->notEmptyString('week');
        
        $validator
            ->add('year', 'valid', [
                'rule' => 'numeric',
                // this is the weeknumber range
                // maximum weeknumber is 53
                'rule' => ['range', 2000, 2100]
                ])
            ->requirePresence('year', 'create')
            ->notEmptyString('year');

        $validator
            ->allowEmptyString('problems');

        $validator
            ->requirePresence('meetings', 'create')
            ->notEmptyString('meetings');

        $validator
            ->allowEmptyString('additional');
        
        $validator
            ->add('created_on', 'valid', ['rule' => 'date'])
            ->requirePresence('created_on', 'create')
            ->notEmptyString('created_on');

        $validator
            ->add('updated_on', 'valid', ['rule' => 'date'])
            ->allowEmptyString('updated_on');
        
        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        $rules->add($rules->existsIn(['project_id'], 'Projects'));
        $rules->add($rules->isUnique(['week', 'year', 'project_id']));
        
        return $rules;
    }
}
