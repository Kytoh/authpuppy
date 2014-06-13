<?php


class apNetworkUserTable extends PluginapNetworkUserTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('apNetworkUser');
    }
}