<?php


class apSimpleNetworkTable extends PluginapSimpleNetworkTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('apSimpleNetwork');
    }
}