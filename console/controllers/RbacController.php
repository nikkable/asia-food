<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...

        // Создадим роли админа, менеджера и пользователя
        $admin = $auth->createRole('admin');
        $manager = $auth->createRole('manager');
        $user = $auth->createRole('user');

        // запишем их в БД
        $auth->add($admin);
        $auth->add($manager);
        $auth->add($user);

        // Создаем разрешения.
        $adminPanel = $auth->createPermission('adminPanel');
        $adminPanel->description = 'Доступ к админ-панели';

        // Запишем их в БД
        $auth->add($adminPanel);

        // Теперь добавим наследование.

        // Менеджер наследует права пользователя
        $auth->addChild($manager, $user);

        // Админ наследует права менеджера
        $auth->addChild($admin, $manager);

        // И еще админ имеет доступ к админ-панели
        $auth->addChild($admin, $adminPanel);

        echo "RBAC roles and permissions have been initialized.\n";
    }

    public function actionAssign($roleName, $userId)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);
        if (!$role) {
            echo "Role '{$roleName}' not found.\n";
            return self::EXIT_CODE_ERROR;
        }

        $auth->assign($role, $userId);
        echo "Role '{$roleName}' has been assigned to user with ID {$userId}.\n";

        return self::EXIT_CODE_NORMAL;
    }
}
