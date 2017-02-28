<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class ArticlesTable extends Table
{
    public function initialize(array $config)
    {
        $this->addBehavior('Timestamp');
        $validator = new Validator();

        $validator
            ->requirePresence('title')
            ->notEmpty('title', 'Please fill this field', 'create')
            ->add('title', [
                'length' => [
                    'rule' => ['minLength', 10],
                    'message' => 'Titles need to be at least 10 characters long',
                ]
            ])
            ->requirePresence('body')
            ->add('body', 'length', [
                'rule' => ['minLength', 50],
                'message' => 'Articles must have a substantial body.'
            ]);
    }
}

 ?>
