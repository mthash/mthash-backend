<?php
trait Timestampable
{
    public $created_at, $updated_at, $deleted_at, $status;

    public function beforeCreate()
    {
        $this->created_at   = time();
        if (!isset ($this->status)) $this->status = 1;
    }

    public function beforeUpdate()
    {
        $this->updated_at   = time();
        if ($this->status == -1)
        {
            $this->deleted_at = time();
        }
    }
}