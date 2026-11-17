<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHlsPathToVideos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('videos', [
            'hls_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'filename',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('videos', 'hls_path');
    }
}
