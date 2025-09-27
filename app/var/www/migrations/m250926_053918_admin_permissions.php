<?php

use yii\db\Migration;

class m250926_053918_admin_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $administratorRole = $auth->createRole('admin');
        $administratorRole->description = 'Administrator';
        $auth->add($administratorRole);

        //  Книги
        $permission = $auth->createPermission('book.index');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('book.index'));

        $permission = $auth->createPermission('book.view');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('book.view'));

        $permission = $auth->createPermission('book.create');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('book.create'));

        $permission = $auth->createPermission('book.update');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('book.update'));

        $permission = $auth->createPermission('book.delete');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('book.delete'));

        //  Авторы
        $permission = $auth->createPermission('author.index');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('author.index'));

        $permission = $auth->createPermission('author.view');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('author.view'));

        $permission = $auth->createPermission('author.create');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('author.create'));

        $permission = $auth->createPermission('author.update');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('author.update'));

        $permission = $auth->createPermission('author.delete');
        $auth->add($permission);
        $auth->addChild($administratorRole, $auth->getPermission('author.delete'));

        $user = \app\models\User::findByUsername('user');
        $auth->assign($administratorRole, $user->id);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->remove($auth->getPermission('book.index'));
        $auth->remove($auth->getPermission('book.view'));
        $auth->remove($auth->getPermission('book.create'));
        $auth->remove($auth->getPermission('book.update'));
        $auth->remove($auth->getPermission('book.delete'));
        $auth->remove($auth->getPermission('author.index'));
        $auth->remove($auth->getPermission('author.view'));
        $auth->remove($auth->getPermission('author.create'));
        $auth->remove($auth->getPermission('author.update'));
        $auth->remove($auth->getPermission('author.delete'));

        $administratorRole = $auth->getRole('admin');
        $user = \app\models\User::findByUsername('user');
        $auth->revoke($administratorRole, $user->id);

        $auth->remove($administratorRole);
    }
}
