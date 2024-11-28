<?php
namespace App\Model\Table;

use App\Model\Entity\Risk;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class RisksTable extends Table
{
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('risks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id',
            'joinType' => 'INNER'
        ]);
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');
        
        $validator
            ->requirePresence('description', 'create')
            ->notEmptyString('description');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        $rules->add($rules->existsIn(['project_id'], 'Projects'));
        
        return $rules;
    }
}
