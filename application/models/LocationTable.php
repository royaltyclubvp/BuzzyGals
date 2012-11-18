<?php

/**
 * Model_LocationTable
 * 
 */
class Model_LocationTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object Model_LocationTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Model_Location');
    }
}