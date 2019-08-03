<?php

class ModelConnect
{

    public $config;

    /**
     * ModelConnect constructor.
     */
    public function __construct()
    {
        $this->config = new Zend_Config_Ini(__DIR__ . '/../inc/config.ini', 'app');
    }

    /**
     * ModelConnect destructor.
     */
    public function __destruct()
    {
        unset($this->config);
    }

    /**
     * @return Model_DB_Logist
     * @throws RestException
     */
    public function DB_Logist()
    {
        $db_logist = new Model_DB_Logist(
            $this->config->db1->host,
            $this->config->db1->username,
            $this->config->db1->password,
            null,
            null,
            $this->config->db1->name,
            $this->config->db1->charset,
            $this->config->db1->timezone
            );
        return $db_logist;
    }

}
