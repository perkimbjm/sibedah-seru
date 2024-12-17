<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js" defer></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js" defer></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js" defer></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js" defer></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js" defer></script>
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
            columns: ':visible'
          }
        },
        {
          extend: 'csv',
          className: 'btn-default',
          text: csvButtonTrans,
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'excel',
          className: 'btn-default',
          text: excelButtonTrans,
          exportOptions: {
            columns: ':visible:not(:first-child):not(:last-child)'
          }
        },
        {
          extend: 'pdf',
          className: 'btn-default',
          text: pdfButtonTrans,
          exportOptions: {
            columns: ':visible'
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