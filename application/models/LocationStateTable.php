<?php

/**
 * Model_LocationTable
 * 
 */
class Model_LocationStateTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object Model_LocationStateTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Model_LocationState');
    }
}