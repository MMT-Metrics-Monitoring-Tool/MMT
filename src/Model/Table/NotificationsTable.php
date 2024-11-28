<?php
namespace App\Model\Table;

use App\Model\Entity\Weeklyreport;
use App\Model\Entity\Comment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class NotificationsTable extends Table
{

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('notifications');
        $this->setPrimaryKey('comment_id', 'member_id');

        $this->belongsTo('Comments', [
            'foreignKey' => 'comment_id'
        ]);
        $this->hasMany('Members', [
            'foreignKey' => 'member_id'
        ]);
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        $rules->add($rules->existsIn(['comment_id'], 'Comments'));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        
        return $rules;
    }
}
