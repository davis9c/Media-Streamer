<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVideoViews extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'video_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'playlist_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],

        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'video_id',
            'videos',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'playlist_id',
            'playlist',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('video_views');
    }

    public function down()
    {
        $this->forge->dropTable('video_views');
    }
}
