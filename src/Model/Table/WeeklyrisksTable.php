<?php
namespace App\Model\Table;

use App\Model\Entity\Weeklyrisk;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WeeklyrisksTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('weeklyrisks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Weeklyreports', [
            'foreignKey' => 'weeklyreport_id',
            'joinType' => 'INNER'
        ]);
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');
        
        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        return $rules;
    }
}
