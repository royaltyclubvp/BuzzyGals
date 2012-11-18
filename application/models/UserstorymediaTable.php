<?php

/**
 * Model_UserstorymediaTable
 */
class Model_UserstorymediaTable extends Doctrine_Table {
    /**
     * Returns an instance of this class.
     *
     * @return object Model_UserstorymediaTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Model_Userstorymedia');
    }
 
}
