<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'admin'; 
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password'];
    protected $returnType = 'array';
}
