<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 14-9-25
 * Time: 下午2:23
 */
class adminMode extends Data {
    function login($user,$password){
        $db=SqlDB::init();
        $password=getPassWord($user,$password);
        $user=$db->quote($user);
        $password=$db->quote($password);
        $sql="select id,username from user_admin where username=$user and password=$password";
        return $db->getExist($sql);
    }
} 