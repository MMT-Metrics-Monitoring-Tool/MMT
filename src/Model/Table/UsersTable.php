<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\I18n\FrozenTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log;

class UsersTable extends Table
{
    use LocatorAwareTrait;

    public function initialize(array $config): void {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        $this->hasMany('Members', [
            'foreignKey' => 'user_id'
        ]);
    }

    public function validationDefault(Validator $validator): \Cake\Validation\Validator {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', null, 'create');

        $validator
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('password', 'create')
            ->notEmptyString('password')
            ->add('password', [
                    'length' => [
                        'rule' => ['minLength', 8],
                        'message' => 'The password has to be at least 8 characters long'
                    ]
            ]);

        $validator
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');
        $validator
            ->requirePresence('role', 'create')
            ->notEmptyString('role')
            ->add('role', 'inList', [
                'rule' => ['inList', ['user', 'admin', 'inactive']],
                'message' => 'Please enter a valid role'
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker {
        $rules->add($rules->isUnique(['email']));
        
        return $rules;
    }

    /**
     * @param $user_id user id to set working hours to target working hours on all projects
     * */
    public function setUserTargetWorkingHoursToCurrentWorkingHours($user_id): void {
        $members = $this->getTableLocator()->get('Members');

        $workinghours = $this->getTableLocator()->get('Workinghours');

        // Get the projects that the user is a member of.
        $members_for_user = $members
            ->find()
            ->select(['id', 'project_id'])
            ->where(['user_id' => $user_id])
            ->toArray();
        
        // For each member role, accumulate the the user's working hours in that project and write the sum as target hours.
        foreach($members_for_user as $member) {
            $workinghours_sum = 0;

            $workinghours_for_projects = $workinghours
                ->find()
                ->select(['duration'])
                ->where(['member_id' => $member['id']])
                ->toArray();

            if (!empty($workinghours_for_projects)) {
                foreach ($workinghours_for_projects as $result) {
                    $workinghours_sum += $result->duration;
                }
            }

            $members->updateAll(
                ['target_hours' => $workinghours_sum],
                ['user_id' => $user_id, 'project_id' => $member['project_id']]
            );
        }
    }
}
