<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Stream Playlist</title>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
        }

        .page {
            width: 92%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 0 40px;
        }

        h1 {
            font-size: 26px;
            margin: 0 0 6px;
        }

        .subtitle {
            margin: 0 0 20px;
            color: #6b7280;
            font-size: 15px;
        }

        .table-wrap {
            overflow-x: auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }

        th,
        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 14px;
            text-align: left;
        }

        th {
            background: #f9fafb;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #4b5563;
            white-space: nowrap;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        tr:hover td {
            background: #f9fafb;
        }

        .stream-link {
            display: inline-block;
            padding: 9px 16px;
            background: #0d6efd;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            white-space: nowrap;
        }

        .stream-link:hover,
        .stream-link:focus {
            background: #0b5ed7;
        }

        .empty {
            text-align: center;
            color: #6b7280;
            padding: 24px;
        }

        @media (max-width: 640px) {
            .page {
                width: 94%;
                padding-top: 16px;
            }

            h1 {
                font-size: 22px;
            }

            th,
            td {
                padding: 10px;
                font-size: 14px;
            }

            .date-col {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <h1>Daftar Stream Playlist</h1>
        <p class="subtitle">Pilih playlist untuk mulai memutar video.</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Playlist</th>
                        <th class="date-col">Tanggal</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($playlists)): ?>
                        <tr>
                            <td colspan="4" class="empty">Belum ada playlist.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($playlists as $i => $playlist): ?>
                        <tr>
                            <td><?= (int) $i + 1 ?></td>
                            <td><?= esc($playlist['playlistname']) ?></td>
                            <td class="date-col"><?= esc((string) $playlist['created_at']) ?></td>
                            <td>
                                <a class="stream-link" href="<?= base_url('stream/' . (int) $playlist['id']) ?>">
                                    &#9654; Stream
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
