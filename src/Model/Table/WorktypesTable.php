<?php
namespace App\Model\Table;

use App\Model\Entity\Worktype;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WorktypesTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('worktypes');
        $this->setDisplayField('description');
        $this->setPrimaryKey('id');

        $this->hasMany('Workinghours', [
            'foreignKey' => 'worktype_id'
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
}
