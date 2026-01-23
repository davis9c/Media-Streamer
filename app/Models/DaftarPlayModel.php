<?php

namespace App\Models;

use CodeIgniter\Model;

class DaftarPlayModel extends Model
{
    protected $table = 'daftar_play';
    protected $allowedFields = ['playlist_id', 'video_id'];
    protected $returnType = 'array';
}
