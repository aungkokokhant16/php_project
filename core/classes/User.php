<?php

class User
{
    public function register($request){
        $error = [];
        if(isset($request)){
            if(empty($request['name'])){
                $error[]='Name Field is Required';
            }
            if(empty($request['email'])){
                $error[]='Email Field is Required';
            }
            if(!filter_var($request['email'],FILTER_VALIDATE_EMAIL)){
                $error[]='Invalid email format';
            }
            if(empty($request['password'])){
                $error[]='Password Field is Required';
            }

            if(count($error))
            {
                return $error;
            }else{
                return true;
            }
        }
    }
}