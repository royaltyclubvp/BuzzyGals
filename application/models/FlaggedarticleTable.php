<?php

/**
 * Model_FlaggedarticleTable
 * 
 */
class Model_FlaggedarticleTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object Model_FlaggedarticleTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Model_Flaggedarticle');
    }
}