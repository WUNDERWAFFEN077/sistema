<?php
namespace App\Middlewares;

use Core\Auth;

class RoleMiddleware {
    public static function isAdmin() {
    	$user = Auth::getCurrentUser();
    	return (int)$user->rol_id === 1;
    }

    public static function isSeller() {
    	$user = Auth::getCurrentUser();     
        //var_dump($user->rol_id);exit();   
        return (int)$user->rol_id === 1 || (int)$user->rol_id === 3;
    }

    public static function isAnalyst() {
    	$user = Auth::getCurrentUser();
    	return (int)$user->rol_id === 1 || (int)$user->rol_id === 2;
    }
}