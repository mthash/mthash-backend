<?php
trait Timestampable
{
    public $created_at, $updated_at, $deleted_at, $status;

    public function beforeCreate()
    {
        if (method_exists(parent::class, 'beforeCreate')) parent::beforeCreate();
        $this->created_at   = time();
        if (!isset ($this->status)) $this->status = 1;
    }

    public function beforeUpdate()
    {
        if (method_exists(parent::class, 'beforeUpdate')) parent::beforeUpdate();
        $this->updated_at   = time();
        if ($this->status == -1)
        {
            $this->deleted_at = time();
        }
    }
}