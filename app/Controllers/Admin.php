<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function upload()
    {
        $video = $this->request->getFile('video');

        if (!$video->isValid()) {
            return redirect()->back()->with('error', 'Upload gagal');
        }

        $newName = $video->getRandomName();
        $video->move(WRITEPATH . 'uploads', $newName);

        return redirect()->back()->with('success', 'Video berhasil diupload');
    }
}
