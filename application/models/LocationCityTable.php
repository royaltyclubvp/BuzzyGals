<?php

/**
 * Model_LocationTable
 * 
 */
class Model_LocationCityTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object Model_LocationCityTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Model_LocationCity');
    }
}