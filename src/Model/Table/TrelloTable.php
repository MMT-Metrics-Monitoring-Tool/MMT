<?php
namespace App\Model\Table;

use App\Model\Entity\Trello;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TrelloTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('trello');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id',
            'joinType' => 'INNER'
        ]);
        
        $this->hasMany('Trellolinks', [
            'foreignKey' => 'trello_id'
        ]);
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');
        
        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        $rules->add($rules->existsIn(['project_id'], 'Projects'));
        
        return $rules;
    }
}
