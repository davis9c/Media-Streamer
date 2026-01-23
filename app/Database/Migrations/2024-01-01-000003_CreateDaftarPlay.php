<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDaftarPlay extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'playlist_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'video_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'position' => [ // ✅ SATU KALI SAJA
                'type' => 'INT',
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'playlist_id',
            'playlist',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'video_id',
            'videos',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->createTable('daftar_play');
    }

    public function down()
    {
        $this->forge->dropTable('daftar_play');
    }
}
