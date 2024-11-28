<?php
namespace App\Model\Table;

use App\Model\Entity\Workinghour;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WorkinghoursTable extends Table
{
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('workinghours');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Weeklyreports', [
            'foreignKey' => 'weeklyreport_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Worktypes', [
            'foreignKey' => 'worktype_id',
            'joinType' => 'INNER'
        ]);
    }
    
    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');

        $validator
            ->notEmptyString('date');

        $validator
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

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
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        $rules->add($rules->existsIn(['worktype_id'], 'Worktypes'));
        return $rules;
    }
}
