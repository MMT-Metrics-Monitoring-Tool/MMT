<?php
namespace App\Model\Table;

use App\Model\Entity\Weeklyreport;
use App\Model\Entity\Comment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class NewreportsTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('newreports');
        $this->setPrimaryKey('weeklyreport_id', 'member_id');

        $this->hasMany('Weeklyreports', [
            'foreignKey' => 'weeklyreport_id'
        ]);
        $this->hasMany('Members', [
            'foreignKey' => 'member_id'
        ]);
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        $rules->add($rules->existsIn(['weeklyreport_id'], 'Weeklyreports'));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        
        return $rules;
    }
}
