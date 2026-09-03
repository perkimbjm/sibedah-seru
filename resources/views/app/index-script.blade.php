<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js" defer></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js" defer></script>
    {{-- pdfmake dimuat on-demand saat tombol Export PDF diklik (lihat loadPdfMake) agar halaman lebih ringan. --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js" defer></script>
<script defer>
    $(function() {
    let copyButtonTrans = 'Copy'
    let csvButtonTrans = 'Export CSV'
    let excelButtonTrans = 'Export Excel'
    let pdfButtonTrans = 'Export PDF'
    let printButtonTrans = 'Print'
    let colvisButtonTrans = 'Show/Hide Column'
    let selectAllButtonTrans = 'Select All'
    let selectNoneButtonTrans = 'Deselect All'

    // ---- Indikator progres saat menyiapkan file ekspor ----
    let ensureExportOverlay = function () {
      let ov = document.getElementById('export-loading-overlay');
      if (!ov) {
        let style = document.createElement('style');
        style.textContent = '@keyframes expSpin{to{transform:rotate(360deg)}}';
        document.head.appendChild(style);

        ov = document.createElement('div');
        ov.id = 'export-loading-overlay';
        ov.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;z-index:99999;display:none;' +
          'align-items:center;justify-content:center;background:rgba(0,0,0,0.45);';
        ov.innerHTML =
          '<div style="background:#fff;padding:24px 32px;border-radius:8px;box-shadow:0 6px 24px rgba(0,0,0,0.25);text-align:center;min-width:220px;">' +
            '<div style="width:2.5rem;height:2.5rem;border:4px solid #cfe2ff;border-top-color:#0d6efd;' +
              'border-radius:50%;animation:expSpin 0.8s linear infinite;margin:0 auto 12px;"></div>' +
            '<div id="export-loading-text" style="font-size:14px;color:#333;">Menyiapkan file, mohon tunggu...</div>' +
          '</div>';
        document.body.appendChild(ov);
      }
      return ov;
    };
    let showExportOverlay = function (msg) {
      let ov = ensureExportOverlay();
      let txt = document.getElementById('export-loading-text');
      if (txt) txt.textContent = msg || 'Menyiapkan file, mohon tunggu...';
      ov.style.display = 'flex';
    };
    let hideExportOverlay = function () {
      let ov = document.getElementById('export-loading-overlay');
      if (ov) ov.style.display = 'none';
    };
    // ---- Pemuatan pdfmake on-demand (hanya saat tombol PDF diklik) ----
    let loadScriptOnce = function (src) {
      return new Promise(function (resolve, reject) {
        let s = document.createElement('script');
        s.src = src;
        s.async = true;
        s.onload = function () { resolve(); };
        s.onerror = function () { reject(new Error('Gagal memuat ' + src)); };
        document.head.appendChild(s);
      });
    };
    let pdfMakePromise = null;
    let loadPdfMake = function () {
      if (window.pdfMake && window.pdfMake.vfs) {
        return Promise.resolve();
      }
      if (!pdfMakePromise) {
        // vfs_fonts.js bergantung pada pdfmake.min.js, jadi dimuat berurutan.
        pdfMakePromise = loadScriptOnce('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js')
          .then(function () {
            return loadScriptOnce('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js');
          })
          .catch(function (err) {
            pdfMakePromise = null; // agar bisa dicoba lagi pada klik berikutnya
            throw err;
          });
      }
      return pdfMakePromise;
    };

    // Menjalankan aksi ekspor bawaan HTML5 dengan overlay progres.
    // setTimeout memberi browser kesempatan merender overlay sebelum proses berat
    // (yang bersifat sinkron/blocking) dijalankan.
    let runExportWithOverlay = function (html5Action, ctx, args, msg) {
      showExportOverlay(msg);
      setTimeout(function () {
        try {
          html5Action.apply(ctx, args);
        } catch (err) {
          console.error('Export error:', err);
          alert('Gagal menyiapkan file ekspor: ' + (err && err.message ? err.message : err));
        } finally {
          // Beri jeda agar unduhan sempat terpicu sebelum overlay ditutup.
          setTimeout(hideExportOverlay, 700);
        }
      }, 50);
    };

    let exportFormatBody = function (data, row, column, node) {
      if (typeof data === 'string') {
        // 1. Strip HTML tags (replace tags with spaces to prevent words from sticking together)
        let cleanData = data.replace(/<[^>]*>/g, ' ').trim();
        
        // 2. Decode HTML entities using textarea element
        let txt = document.createElement("textarea");
        txt.innerHTML = cleanData;
        cleanData = txt.value;
        
        // 3. Normalize multiple whitespace characters
        cleanData = cleanData.replace(/\s+/g, ' ').trim();
        
        // 4. If it's a NIK (exactly 16 digits), prepend Zero Width Non-Joiner to preserve string format
        if (/^\d{16}$/.test(cleanData)) {
          return '\u200C' + cleanData;
        }
        
        return cleanData;
      }
      return data;
    };

    let languages = {
      'id': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Indonesian.json',
          'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json'
    };

    $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, { className: 'btn' })
    $.extend(true, $.fn.dataTable.defaults, {
      language: {
        url: languages['{{ app()->getLocale() }}']
      },
      columnDefs: [{
          orderable: false,
          className: 'select-checkbox',
          targets: 0
      }, {
          orderable: false,
          searchable: false,
          targets: -1
      }],
      select: {
        style:    'multi+shift',
        selector: 'td:first-child'
      },
      order: [],
      scrollX: true,
      pageLength: 100,
      dom: 'lBfrtip<"actions">',
      buttons: [
        {
          extend: 'selectAll',
          className: 'btn-primary',
          text: selectAllButtonTrans,
          exportOptions: {
            columns: ':visible'
          },
          action: function(e, dt) {
            e.preventDefault()
            dt.rows().deselect();
            dt.rows({ search: 'applied' }).select();
          }
        },
        {
          extend: 'selectNone',
          className: 'btn-primary',
          text: selectNoneButtonTrans,
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'copy',
          className: 'btn-default',
          text: copyButtonTrans,
          exportOptions: {
            columns: ':visible',
            format: {
              body: exportFormatBody
            }
          }
        },
        {
          extend: 'csv',
          className: 'btn-default',
          text: csvButtonTrans,
          exportOptions: {
            columns: ':visible',
            format: {
              body: exportFormatBody
            }
          },
          action: function (e, dt, node, config) {
            runExportWithOverlay(
              $.fn.dataTable.ext.buttons.csvHtml5.action,
              this, [e, dt, node, config], 'Menyiapkan CSV, mohon tunggu...'
            );
          }
        },
        {
          extend: 'excel',
          className: 'btn-default',
          text: excelButtonTrans,
          exportOptions: {
            columns: ':visible:not(:first-child):not(:last-child)',
            format: {
              body: exportFormatBody
            }
          },
          action: function (e, dt, node, config) {
            runExportWithOverlay(
              $.fn.dataTable.ext.buttons.excelHtml5.action,
              this, [e, dt, node, config], 'Menyiapkan Excel, mohon tunggu...'
            );
          }
        },
        {
          extend: 'pdf',
          className: 'btn-default',
          text: pdfButtonTrans,
          // pdfmake dimuat on-demand, jadi paksa tombol tetap tampil walau
          // window.pdfMake belum ada saat tabel diinisialisasi.
          available: function () { return true; },
          orientation: 'landscape',
          pageSize: 'A4',
          exportOptions: {
            columns: ':visible'
          },
          // Muat semua kolom dalam satu halaman: lebar halaman dinamis mengikuti
          // jumlah kolom, kolom dibentangkan merata, font & padding disesuaikan.
          customize: function (doc) {
            // Cari elemen tabel di dalam dokumen.
            let tableNode = null;
            for (let i = 0; i < doc.content.length; i++) {
              if (doc.content[i] && doc.content[i].table) {
                tableNode = doc.content[i];
                break;
              }
            }
            let colCount = (tableNode && tableNode.table.body[0]) ? tableNode.table.body[0].length : 0;

            // Lebar halaman dinamis agar seluruh kolom muat pada satu halaman (lebar).
            // ~55pt per kolom, minimal selebar A4 landscape (842pt). Tinggi ~ A4 landscape.
            let pageWidth = Math.max(842, colCount * 55 + 60);
            doc.pageSize = { width: pageWidth, height: 595 };
            doc.pageMargins = [20, 24, 20, 24];

            // Ukuran font menyesuaikan banyaknya kolom.
            let fontSize = colCount > 20 ? 6 : (colCount > 12 ? 7 : 8);
            doc.defaultStyle.fontSize = fontSize;
            if (doc.styles.tableHeader) {
              doc.styles.tableHeader.fontSize = fontSize;
              doc.styles.tableHeader.alignment = 'center';
            }

            if (tableNode) {
              // Bentangkan seluruh kolom merata selebar halaman.
              tableNode.table.widths = Array(colCount).fill('*');
              // Padding sel diperkecil agar lebih ringkas.
              tableNode.layout = {
                hLineWidth: function () { return 0.5; },
                vLineWidth: function () { return 0.5; },
                hLineColor: function () { return '#bbbbbb'; },
                vLineColor: function () { return '#bbbbbb'; },
                paddingLeft: function () { return 2; },
                paddingRight: function () { return 2; },
                paddingTop: function () { return 1; },
                paddingBottom: function () { return 1; }
              };
            }
          },
          action: function (e, dt, node, config) {
            let self = this;
            let args = [e, dt, node, config];
            showExportOverlay('Memuat komponen PDF, mohon tunggu...');
            loadPdfMake().then(function () {
              runExportWithOverlay(
                $.fn.dataTable.ext.buttons.pdfHtml5.action,
                self, args, 'Menyiapkan PDF, mohon tunggu...'
              );
            }).catch(function (err) {
              hideExportOverlay();
              console.error(err);
              alert('Komponen PDF gagal dimuat (periksa koneksi internet), silakan coba lagi.');
            });
          }
        },
        {
          extend: 'print',
          className: 'btn-default',
          text: printButtonTrans,
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'colvis',
          className: 'btn-default',
          text: colvisButtonTrans,
          exportOptions: {
            columns: ':visible'
          }
        }
      ]
    });

    $.fn.dataTable.ext.classes.sPageButton = '';
  });
</script>