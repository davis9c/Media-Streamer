<?php

namespace App\Models;

use CodeIgniter\Model;

class PlaylistModel extends Model
{
    protected $table = 'playlist';
    protected $primaryKey = 'id';
    protected $allowedFields = ['playlistname'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';
}
