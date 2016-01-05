<?php
namespace StoreFinder\Model;

use Eloquent;

Class Setting extends Eloquent
{

    protected $table='settings';

	// Disabling Auto Timestamps
    public $timestamps = false;

    public function users()
    {
        return $this->hasOne('StoreFinder\Model\User');
    }

}