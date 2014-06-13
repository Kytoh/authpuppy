<?php


class apUserRememberMeTable extends PluginapUserRememberMeTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('apUserRememberMe');
    }
}