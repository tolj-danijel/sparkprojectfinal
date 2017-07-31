<?php 

class Database
{

    private static $db;

    public function getInstance()
    {
        if(static::$db==null)
            static::$db=mysqli_connect('localhost','root','','rentacar');
        return static::$db;
    }

    public function userExists($username)
    {
            $query = "SELECT * FROM users WHERE username='$username'"; 

            if ($result = mysqli_query(self::getInstance(), $query))
         {
            $rows = mysqli_num_rows($result); 

            if ($rows > 0) 
                return true;
            return false;
         }
    }
    public function registerUser($data)
    {
         $username=$data['username'];
         $password=hash('sha256',$data['password']);

         $query="INSERT INTO users(username,password_hash)
                 VALUES('$username','$password')";
         if($result = mysqli_query(Database::getInstance(), $query))
          {
            if (mysqli_affected_rows(self::getInstance()) == 1)
                return true;
          
          }
          return false;
     }   

}