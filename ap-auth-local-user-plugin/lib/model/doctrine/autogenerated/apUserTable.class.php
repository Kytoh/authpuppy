<?php


class apUserTable extends PluginapUserTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('apUser');
    }
}