<h3>Top Video</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Judul Video</th>
        <th>Jumlah View</th>
    </tr>
    <?php foreach ($topVideos as $i => $v): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($v['title']) ?></td>
            <td><?= $v['total'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h3>View per Playlist</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Playlist</th>
        <th>Jumlah View</th>
    </tr>
    <?php foreach ($playlistViews as $i => $p): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($p['playlistname']) ?></td>
            <td><?= $p['total'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>