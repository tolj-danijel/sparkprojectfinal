<?php


namespace App\Controllers\Validation;


use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;



class Validator 
{
    public function validate($request,$rules)
    {
        foreach($rules as $field=>$rule)
        {
            try
            {
            $rule->setName($field)->assert($request->getParam($field));    
            }
            catch(NestedValidationException $exception)
            {   
                    $this->errors["$field"]=($exception->getMessages());
            }

        }

       $_SESSION['errors'] = $this->errors;
       if(isset($this->errors ))
        var_dump($this->errors);

        return $this;
    }


    public function failed()
    {
        return !empty($this->errors);
    }
}