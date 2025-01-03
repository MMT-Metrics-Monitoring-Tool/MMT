<?php
namespace App\Model\Table;

use App\Model\Entity\Trellolink;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TrellolinksTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('trellolinks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Trello', [
            'foreignKey' => 'trello_id',
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
        $rules->add($rules->existsIn(['trello_id'], 'Trello'));
        
        return $rules;
    }
}
