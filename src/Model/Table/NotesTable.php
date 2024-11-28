<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class NotesTable extends Table
{
    
    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('notes');
        $this->setDisplayField('content');
        $this->setPrimaryKey('id');
    }
    
    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');

        $validator
            ->requirePresence('content', 'create')
            ->notEmptyString('content');

        $validator
            ->notEmptyString('project_role');
        
        $validator
            ->notEmptyString('created_on');
        
        $validator
            ->allowEmptyString('email');
        
        $validator
            ->allowEmptyString('contact_user');
        
        $validator
            ->allowEmptyString('note_read');

        return $validator;
    }
}
