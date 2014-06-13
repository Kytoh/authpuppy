<?php


class apPhysicalUserTable extends PluginapPhysicalUserTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('apPhysicalUser');
    }
}