<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.02.2021
 * Time: 22:39
 */

namespace frontend\models;


class PresentStrategy
{
    protected $service;
    public function __construct(IService $service)
    {
        $this->service = $service;
    }

    public function run(){
       return $this->service->getPresent();
    }

}