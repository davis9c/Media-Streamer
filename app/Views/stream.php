<!DOCTYPE html>
<html>

<head>
    <title>Stream Playlist</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #f4f4f4;
        }
    </style>
</head>

<body>

    <h2>Daftar Stream Playlist</h2>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Playlist</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>

        <?php if (empty($playlists)): ?>
            <tr>
                <td colspan="4" align="center">Belum ada playlist</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($playlists as $i => $playlist): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($playlist['playlistname']) ?></td>
                <td><?= esc($playlist['created_at']) ?></td>
                <td>
                    <a href="/stream/<?= $playlist['id'] ?>">▶ Stream</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>