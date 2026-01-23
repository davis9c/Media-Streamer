<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoModel extends Model
{
    protected $table = 'videos';
    protected $primaryKey = 'id';

    protected $allowedFields = ['title', 'filename', 'created_at'];

    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
}
