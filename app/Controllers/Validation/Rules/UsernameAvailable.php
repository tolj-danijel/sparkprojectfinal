<?php



namespace App\Controllers\Validation\Rules; 

use Respect\Validation\Rules\AbstractRule;

use App\Models\User;

class UsernameAvailable extends AbstractRule
{
    public function validate($input)
    {
        return User::where('username',$input)->get()->count()===0;
    }
}