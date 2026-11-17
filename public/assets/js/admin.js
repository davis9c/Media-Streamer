(function () {
    'use strict';

    function initServerTable(tableId) {
        var table = document.getElementById(tableId);

        if (!table || !window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) {
            return;
        }

        var $table = window.jQuery(table);
        var url = table.getAttribute('data-url');

        if (!url) {
            return;
        }

        $table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                dataSrc: 'data'
            },
            columns: [
                { data: 'no', orderable: false, searchable: false, className: 'text-center' },
                { data: 'name' },
                { data: 'created_at' },
                { data: 'action', orderable: false, searchable: false, className: 'text-nowrap' }
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                processing: 'Memuat...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords: 'Data tidak ditemukan',
                emptyTable: 'Belum ada data',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initServerTable('videoTable');
        initServerTable('playlistTable');

        // Tombol "View" pada DataTables memutar video di panel upload.
        document.addEventListener('click', function (event) {
            var target = event.target;

            while (target && target !== document && !target.classList.contains('js-play-video')) {
                target = target.parentNode;
            }

            if (!target || target === document || !target.classList.contains('js-play-video')) {
                return;
            }

            if (typeof window.playVideo === 'function') {
                window.playVideo(target.getAttribute('data-filename'));
            }
        });
    });
})();
