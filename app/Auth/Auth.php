<?php


namespace App\Auth;

use App\Models\User;

class Auth
{

    public function user()
    {
        if($this::check())
        {
        return User::where('user_id',$_SESSION['user'])->get()->first();
        }
    }

    public function check()
    {
        return isset($_SESSION['user']);
    }

    public function checkIfAdmin()
    {
        if(!$this->check())
            return false;

        $user= User::where('user_id',$_SESSION['user'])->get()->first();


        return $user->username == $GLOBALS['adminuname'] && $user->password_hash=$GLOBALS['adminpw'];
    }

    public function attempt($username,$password)
    {
       if($user= User::where('username',$username)->get()->first())
          {
               if(password_verify($password,$user->password_hash))
                      {
                         $_SESSION['user'] = $user->user_id;
                         return true;
                      }
          }
        return false;
          
   }

   public function logout()
   {
       
       unset($_SESSION['user']);
   }
}
