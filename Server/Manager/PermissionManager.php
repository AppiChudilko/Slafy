<?php

namespace Server\Manager;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Request
 */
class PermissionManager
{
    /*
     * Последние 2 числа = права, все числа до этого = id проекта.
     * 0 = Соц. сеть
     * 1 = Журнал
     * */

    protected $permissions = [
        -1 => ['name' => 'Не активирован', 'is_active' => 0, 'is_editor' => 0, 'is_moder' => 0, 'is_admin' => 0],
        0 => ['name' => 'Пользователь', 'is_active' => 1, 'is_editor' => 0, 'is_moder' => 0, 'is_admin' => 0],
        70 => ['name' => 'Редактор', 'is_active' => 1, 'is_editor' => 1, 'is_moder' => 0, 'is_admin' => 0],
        80 => ['name' => 'Модератор', 'is_active' => 1, 'is_editor' => 1, 'is_moder' => 1, 'is_admin' => 0],
        90 => ['name' => 'Администратор', 'is_active' => 1, 'is_editor' => 1, 'is_moder' => 1, 'is_admin' => 1],
        170 => ['name' => 'Редактор', 'is_active' => 1, 'is_editor' => 1, 'is_moder' => 0, 'is_admin' => 0],
        180 => ['name' => 'Модератор', 'is_active' => 1, 'is_editor' => 1, 'is_moder' => 1, 'is_admin' => 0],
        190 => ['name' => 'Администратор', 'is_active' => 1, 'is_editor' => 1, 'is_moder' => 1, 'is_admin' => 1],
        9999 => ['name' => 'Разработчик', 'is_active' => 1, 'is_editor' => 1, 'is_moder' => 1, 'is_admin' => 1]
    ];

    /**
     * @param $permissionId
     * @return array
     */
    public function getPermission($permissionId) {
        return (isset($this->permissions[$permissionId]) ? $this->permissions[$permissionId] : null);
    }

    /**
     * @param $permissionId
     * @return string
     */
    public function getPermissionName($permissionId) {
        if($permission = $this->getPermission($permissionId))
            return $permission['name'];
        return null;
    }

    /**
     * @param $permissionId
     * @return bool
     */
    public function permissionIsActive($permissionId) {
        if($permission = $this->getPermission($permissionId))
            return $permission['is_active'];
        return false;
    }

    /**
     * @param $permissionId
     * @return bool
     */
    public function permissionIsEditor($permissionId) {
        if($permission = $this->getPermission($permissionId))
            return $permission['is_editor'];
        return false;
    }

    /**
     * @param $permissionId
     * @return bool
     */
    public function permissionIsModer($permissionId) {
        if($permission = $this->getPermission($permissionId))
            return $permission['is_moder'];
        return false;
    }

    /**
     * @param $permissionId
     * @return bool
     */
    public function permissionIsAdmin($permissionId) {
        if($permission = $this->getPermission($permissionId))
            return $permission['is_admin'];
        return false;
    }

    /**
     * @param $permissionId
     * @return bool
     */
    public function permissionIsDeveloper($permissionId) {
        return ($permissionId === 9999) ? true : false;
    }
}