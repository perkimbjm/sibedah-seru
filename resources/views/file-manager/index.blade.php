@extends('layouts.main')

@php
    $menuName = 'File Manager';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="p-0 m-0 bg-transparent breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('app.file-manager.index') }}">Root</a>
                                    </li>
                                    @foreach($breadcrumbs as $breadcrumb)
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('app.file-manager.index', ['path' => $breadcrumb['path']]) }}">
                                                {{ $breadcrumb['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        </div>
                        <div>
                            <div class="mr-2 btn-group">
                                <button type="button" class="btn btn-default" onclick="switchView('table')" id="tableViewBtn">
                                    <i class="fas fa-list"></i>
                                </button>
                                <button type="button" class="btn btn-default" onclick="switchView('grid')" id="gridViewBtn">
                                    <i class="fas fa-th-large"></i>
                                </button>
                            </div>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                                <i class="fas fa-upload"></i> Upload File
                            </button>
                            <button class="btn btn-success" data-toggle="modal" data-target="#createFolderModal">
                                <i class="fas fa-folder-plus"></i> New Folder
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="tableView" class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50%">Name</th>
                                    <th>Size</th>
                                    <th>Last Modified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                    @if($path !== '')
                                        <tr>
                                            <td colspan="4">
                                                <a href="{{ route('app.file-manager.index', ['path' => dirname($path)]) }}" class="text-decoration-none">
                                                    <i class="fas fa-level-up-alt"></i> Kembali ke Folder Sebelumnya
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach($directories as $directory)
                                        <tr class="file-item"
                                            data-type="directory"
                                            data-path="{{ $directory['path'] }}"
                                            data-name="{{ $directory['name'] }}">
                                            <td>
                                                <a href="{{ route('app.file-manager.index', ['path' => $directory['path']]) }}" class="text-lg text-decoration-none">
                                                    <i class="fas fa-folder text-warning"></i>
                                                    {{ $directory['name'] }}
                                                </a>
                                            </td>
                                            <td>-</td>
                                            <td>{{ $directory['last_modified'] }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="deleteItems('{{ $directory['path'] }}', 'directory')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach($files as $file)
                                        <tr class="file-item"
                                            data-type="file"
                                            data-path="{{ $file['path'] }}"
                                            data-name="{{ $file['name'] }}"
                                            data-extension="{{ $file['extension'] }}">
                                            <td>
                                                <i class="fas {{ getFileIconClass($file['extension']) }}"></i>
                                                {{ $file['name'] }}
                                            </td>
                                            <td>{{ $file['size'] }}</td>
                                            <td>{{ $file['last_modified'] }}</td>
                                            <td>
                                                <a href="{{ Storage::url($file['path']) }}" class="mb-1 btn btn-sm btn-primary" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="mb-1 btn btn-sm btn-danger" onclick="deleteItems('{{ $file['path'] }}', 'file')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="view-grid d-none" id="gridView">
                        @if($path !== '')
                            <div class="grid-item" onclick="window.location.href='{{ route('app.file-manager.index', ['path' => dirname($path)]) }}'">
                                <i class="fas fa-level-up-alt"></i>
                                <span class="grid-item-name">Kembali ke Folder Sebelumnya</span>
                            </div>
                        @endif

                        @foreach($directories as $directory)
                            <div class="grid-item file-item"
                                data-type="directory"
                                data-path="{{ $directory['path'] }}"
                                data-name="{{ $directory['name'] }}"
                                ondblclick="window.location.href='{{ route('app.file-manager.index', ['path' => $directory['path']]) }}'"
                                oncontextmenu="event.preventDefault(); showContextMenu(event, this)">
                                <i class="fas fa-folder icon-folder"></i>
                                <span class="grid-item-name">{{ $directory['name'] }}</span>
                            </div>
                        @endforeach


                        @foreach($files as $file)
                            <div class="grid-item file-item"
                                data-type="file"
                                data-path="{{ $file['path'] }}"
                                data-name="{{ $file['name'] }}"
                                data-extension="{{ $file['extension'] }}"
                                oncontextmenu="event.preventDefault(); showContextMenu(event, this)">
                                @if(in_array($file['extension'], ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                <img src="{{ route('file-manager.thumbnail', ['path' => $file['path']]) }}" alt="{{ $file['name'] }}" class="img-thumbnail">
                                @else
                                    <i class="fas {{ getFileIconClass($file['extension']) }} fa-3x"></i>
                                @endif
                                <span class="grid-item-name">{{ $file['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Files</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" action="{{ route('app.file-manager.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="path" value="{{ $path }}">
                    <div class="form-group">
                        <label>Select Files</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="files" name="files[]" multiple required>
                            <label class="custom-file-label" for="files">Choose files...</label>
                        </div>
                    </div>

                    <div id="selectedFiles" class="mt-3">
                        <!-- Selected files will be listed here -->
                    </div>

                    <div id="uploadProgress" class="mt-3" style="display: none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="mt-1 text-muted" id="uploadStatus"></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="uploadButton">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">Create New Folder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('app.file-manager.create-folder') }}" method="POST">
                @csrf
                <input type="hidden" name="path" value="{{ $path }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Folder Name</label>
                        <input type="text" name="folder_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div id="contextMenu" class="context-menu">
    <ul>
        <li onclick="copyItems()"><i class="mr-2 fas fa-copy"></i>Copy</li>
        <li onclick="cutItems()"><i class="mr-2 fas fa-cut"></i>Cut</li>
        <li onclick="pasteItem()" id="pasteMenuItem" class="disabled"><i class="mr-2 fas fa-paste"></i>Paste</li>
        <li onclick="deleteItems()"><i class="mr-2 fas fa-trash"></i>Delete</li>
        <li onclick="downloadItems()"><i class="mr-2 fas fa-download"></i>Download</li>
        <li onclick="extractZip()" id="extractMenuItem" class="disabled"><i class="mr-2 fas fa-file-archive"></i>Extract Here</li>
        <li onclick="renameItem()"><i class="mr-2 fas fa-edit"></i>Rename</li>
    </ul>
</div>

<!-- Rename Modal -->
<div class="modal fade" id="renameModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rename Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="newFileName" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmRename()">Rename</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('styles')
<!-- Toast CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .context-menu {
        display: none;
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        top: 0;
        left: 0;
    }

    .context-menu ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .context-menu li {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .context-menu li:last-child {
        border-bottom: none;
    }

    .context-menu li:hover {
        background: #f0f0f0;
    }

    .context-menu li.disabled {
        color: #999;
        cursor: not-allowed;
        opacity: 0.5;
    }

    .context-menu li.enabled {
        color: #000;
        cursor: pointer;
        opacity: 1;
    }

    .file-item {
        cursor: context-menu;
    }

    .view-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .grid-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .grid-item:hover {
        background-color: #f8f9fa;
    }


    .grid-item i {
        font-size: 48px;
        margin-bottom: 10px;
    }

    .grid-item-name {
        font-size: 0.9rem;
        word-break: break-word;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .icon-folder {
        color: #ffd700;
    }

    .icon-file {
        color: #6c757d;
    }

    .icon-file-image {
        color: #9b17f9;
    }

    .icon-file-pdf {
        color: #dc3545;
    }

    .icon-file-word {
        color: #007bff;
    }

    .icon-file-excel {
        color: #28a745;
    }

    .icon-file-archive {
        color: #df2caf;
    }

    .grid-item.icon-file-code {
        color: #f8312f;
    }

    .grid-item.icon-file-alt {
        color: #17a2b8;
    }

    .selected-file {
        padding: 5px 10px;
        margin-bottom: 5px;
        background-color: #f8f9fa;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    #uploadProgress {
        margin-top: 15px;
    }

    #uploadProgress .progress {
        height: 20px;
    }

    #uploadStatus {
        display: block;
        margin-top: 5px;
        font-size: 0.8rem;
    }

    .file-item.selected {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .file-item.selected td {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .grid-item.selected {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .img-thumbnail {
        max-width: 150px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 5px;
        margin-bottom: 10px;
    }

</style>
@endsection

@section('scripts')
<!-- Axios -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.7/axios.min.js"></script>
<!-- Toast JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    let contextMenu = document.getElementById('contextMenu');
    let clipboard = null;
    let clipboardOperation = null;
    let selectedItems = new Set();
    let lastClickedItem = null;


    // CSRF Token setup untuk Axios
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function getClipboard() {
        try {
            return JSON.parse(localStorage.getItem('fileManagerClipboard'));
        } catch {
            return null;
        }
    }

    function setClipboard(data) {
        try {
            localStorage.setItem('fileManagerClipboard', JSON.stringify(data));
            updateContextMenuOptions();
        } catch (e) {
            console.error('Error setting clipboard:', e);
            toastr.error('Error copying to clipboard');
        }
    }

    const clearClipboard = () => {
        localStorage.removeItem('fileManagerClipboard');
        updatePasteMenuItem();
    };

    // Update paste menu item berdasarkan clipboard
    function updatePasteMenuItem() {
        const clipboard = getClipboard();
        const pasteMenuItem = document.getElementById('pasteMenuItem');

        if (clipboard && clipboard.path) {
            pasteMenuItem.classList.remove('disabled');
        } else {
            pasteMenuItem.classList.add('disabled');
        }
    }

    // Context Menu Setup
    document.addEventListener('click', hideContextMenu);

    function logSelection(msg) {
    console.log(msg, {
        selectedCount: selectedItems.size,
        lastClicked: lastClickedItem?.dataset?.path
    });
}

function toggleItemSelection(item, event) {
    console.log('Click event:', {
        ctrl: event.ctrlKey,
        shift: event.shiftKey,
        itemPath: item.dataset.path
    });

    // Ctrl click
    if (event.ctrlKey) {
        console.log('Ctrl click detected');

        if (selectedItems.has(item)) {
            item.classList.remove('selected');
            selectedItems.delete(item);


        } else {
            // Jika belum dipilih, tambahkan seleksi dan beri gaya selected
            item.classList.add('selected');
            selectedItems.add(item);
        }
        logSelection('After Ctrl selection');
        // Ctrl click tidak akan mempengaruhi lastClickedItem
        return;  // Kembali tanpa memperbarui lastClickedItem
    }
    // Shift click
    else if (event.shiftKey && lastClickedItem) {
        console.log('Shift click detected');
        const items = Array.from(document.querySelectorAll('.file-item'));

        // Temukan indeks item pertama dan terakhir yang diklik
        const startIdx = items.indexOf(lastClickedItem);
        const endIdx = items.indexOf(item);

        if (startIdx !== -1 && endIdx !== -1) {
            const start = Math.min(startIdx, endIdx);
            const end = Math.max(startIdx, endIdx);

            // JANGAN hapus seleksi sebelumnya, tambahkan seleksi baru
            for (let i = start; i <= end; i++) {
                items[i].classList.add('selected');
                selectedItems.add(items[i]);
            }
            logSelection('After Shift selection');
        } else {
            console.error('Invalid index for range selection');
        }

        lastClickedItem = item; // Set item terakhir yang diklik saat shift digunakan
    }
    // Normal click
    else {
        console.log('Normal click detected');
        // Hapus semua seleksi sebelumnya
        document.querySelectorAll('.file-item').forEach(item => {
            item.classList.remove('selected');
        });
        selectedItems.clear();

        // Pilih item yang di-klik
        item.classList.add('selected');
        selectedItems.add(item);
        logSelection('After normal selection');

        lastClickedItem = item; // Set item terakhir yang diklik hanya untuk klik normal
    }

    updateContextMenuOptions(); // Perbarui opsi menu konteks
}






    // Update context menu berdasarkan selection
    function updateContextMenuOptions() {
        const hasSelection = selectedItems.size > 0;
        const contextMenuItems = contextMenu.querySelectorAll('li');

        // Update Extract Here option
        const extractMenuItem = document.getElementById('extractMenuItem');
        const isZipSelected = Array.from(selectedItems).some(item => item.dataset.extension === 'zip');
        if (isZipSelected) {
            extractMenuItem.classList.remove('disabled');
            extractMenuItem.classList.add('enabled');
        } else {
            extractMenuItem.classList.add('disabled');
            extractMenuItem.classList.remove('enabled');
        }

        // Update Paste option
        const pasteMenuItem = document.getElementById('pasteMenuItem');
        const clipboard = getClipboard();
        if (clipboard && clipboard.items && clipboard.items.length > 0) {
            pasteMenuItem.classList.remove('disabled');
            pasteMenuItem.classList.add('enabled');
        } else {
            pasteMenuItem.classList.add('disabled');
            pasteMenuItem.classList.remove('enabled');
        }

        // Update other context menu items
        contextMenuItems.forEach(item => {
            if (!item.id.includes('paste') && !item.id.includes('extract')) {
                item.classList.toggle('disabled', !hasSelection);
            }
        });
    }

    function showContextMenu(event, item) {
        event.preventDefault();

        if (!selectedItems.has(item) && !event.ctrlKey && !event.shiftKey) {
            selectedItems.forEach(selectedItem => {
                selectedItem.classList.remove('selected');
            });
            selectedItems.clear();
            item.classList.add('selected');
            selectedItems.add(item);
        }

        contextMenu.style.display = 'block';
        contextMenu.style.left = event.pageX + 'px';
        contextMenu.style.top = event.pageY + 'px';

        updateContextMenuOptions();
    }

    function hideContextMenu() {
        contextMenu.style.display = 'none';
    }



    // File Operations
    function renameItem() {
        hideContextMenu();
        const selectedItem = Array.from(selectedItems)[0];
        if (!selectedItem) return;
        $('#newFileName').val(selectedItem.dataset.name);
        $('#renameModal').modal('show');
    }

    function confirmRename() {
        const selectedItem = Array.from(selectedItems)[0];
        if (!selectedItem) return;
        const newName = $('#newFileName').val();
        const oldPath = selectedItem.dataset.path;

        axios.post('{{ route('app.file-manager.rename') }}', {
            old_path: oldPath,
            new_name: newName
        })
        .then(response => {
            toastr.success('Item renamed successfully');
            setTimeout(() => window.location.reload(), 1000);
        })
        .catch(error => {
            toastr.error(error.response?.data?.message || 'Error renaming item');
        });

        $('#renameModal').modal('hide');
    }

    // Modifikasi fungsi operasi file untuk mendukung multi-select
    function copyItems() {
        hideContextMenu();
        const items = Array.from(selectedItems).map(item => ({
            path: item.dataset.path,
            name: item.dataset.name,
            type: item.dataset.type
        }));

        setClipboard({
            items: items,
            operation: 'copy'
        });
        toastr.info(`${items.length} item(s) copied to clipboard`);
    }

    function cutItems() {
        hideContextMenu();
        const items = Array.from(selectedItems).map(item => ({
            path: item.dataset.path,
            name: item.dataset.name,
            type: item.dataset.type
        }));

        setClipboard({
            items: items,
            operation: 'cut'
        });
        toastr.info(`${items.length} item(s) cut to clipboard`);
    }

    function deleteItems() {
        hideContextMenu();
        const items = Array.from(selectedItems);

        if (confirm(`Are you sure you want to delete ${items.length} item(s)?`)) {
            const deletePromises = items.map(item => {
                return axios.delete('{{ route('app.file-manager.delete') }}', {
                    data: {
                        path: item.dataset.path,
                        type: item.dataset.type
                    }
                });
            });

            Promise.all(deletePromises)
                .then(() => {
                    toastr.success(`${items.length} item(s) deleted successfully`);
                    setTimeout(() => window.location.reload(), 1000);
                })
                .catch(error => {
                    toastr.error(error.response?.data?.message || 'Error deleting items');
                });
        }
    }

    function pasteItem() {
        const clipboard = getClipboard();
        if (!clipboard || !clipboard.items || clipboard.items.length === 0) return;

        hideContextMenu();
        const currentPath = '{{ $path }}';
        let processed = 0;
        let errors = [];

        function processPasteItem(index, replace = false) {
            if (index >= clipboard.items.length) {
                // All items processed
                if (errors.length > 0) {
                    errors.forEach(error => toastr.error(error));
                }
                if (processed > 0) {
                    toastr.success(`Successfully pasted ${processed} item(s)`);
                    if (clipboard.operation === 'cut') {
                        clearClipboard();
                    }
                    setTimeout(() => window.location.reload(), 1000);
                }
                return;
            }

            const item = clipboard.items[index];
            const fileName = item.name;
            const destinationPath = currentPath ? `${currentPath}/${fileName}` : fileName;

            const endpoint = clipboard.operation === 'copy'
                ? '{{ route('app.file-manager.copy') }}'
                : '{{ route('app.file-manager.move') }}';

            axios.post(endpoint, {
                source_path: item.path,
                destination_path: destinationPath,
                replace: replace
            })
            .then(response => {
                processed++;
                processPasteItem(index + 1);
            })
            .catch(error => {
                if (error.response?.status === 409) {
                    // File exists, ask for confirmation
                    if (confirm(`File "${fileName}" already exists. Do you want to replace it?`)) {
                        processPasteItem(index, true);
                    } else {
                        processPasteItem(index + 1);
                    }
                } else {
                    errors.push(`Error pasting "${fileName}": ${error.response?.data?.message || 'Unknown error'}`);
                    processPasteItem(index + 1);
                }
            });
        }

        // Start processing items
        processPasteItem(0);

        // Clear clipboard after paste operation
        if (clipboard.operation === 'copy') {
            clearClipboard();
        }

    }

    // Initialize clipboard state when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updatePasteMenuItem();
        document.addEventListener('click', function() {
            updatePasteMenuItem();
        });
    });

    function extractZip() {
        const selectedItem = Array.from(selectedItems).find(item => item.dataset.extension === 'zip');
        if (!selectedItem) return;

        hideContextMenu();

        axios.post('{{ route('app.file-manager.extract') }}', {
            zip_path: selectedItem.dataset.path
        })
        .then(response => {
            toastr.success('File extracted successfully');
            setTimeout(() => window.location.reload(), 1000);
        })
        .catch(error => {
            toastr.error(error.response?.data?.message || 'Error extracting file');
        });
    }

    function downloadItems() {
    if (selectedItems.size === 0) return;

    const items = Array.from(selectedItems);
    const paths = items.map(item => item.dataset.path);

    // Jika file tunggal
    if (items.length === 1 && items[0].dataset.type !== 'directory') {
        const path = items[0].dataset.path;
        const filename = path.split('/').pop();

        fetch('/app/file-manager/download-file', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ path: path })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.blob();
        })
        .then(blob => {
            if (!(blob instanceof Blob)) {
                throw new Error('Response is not a valid blob');
            }

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error downloading file:', error);
            alert('Error downloading file. Please try again.');
        });
        return;
    }

    // Untuk direktori atau multiple files
    fetch('/app/file-manager/download-items', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ paths: paths })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.blob();
    })
    .then(blob => {
        if (!(blob instanceof Blob)) {
            throw new Error('Response is not a valid blob');
        }

        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;

        // Set nama file zip
        if (items.length === 1 && items[0].dataset.type === 'directory') {
            a.download = `${items[0].dataset.path.split('/').pop()}.zip`;
        } else {
            a.download = 'selected_files.zip';
        }

        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    })
    .catch(error => {
        console.error('Error downloading items:', error);
        alert('Error downloading items. Please try again.');
    });
}

    // Update table rows to add context menu
    document.querySelectorAll('tr').forEach(row => {
        if (row.dataset.path) {
            row.classList.add('file-item');
            row.addEventListener('contextmenu', (e) => showContextMenu(e, row));
        }
    });



    // Setup Toast Notifications
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };

    // Tampilkan pesan sukses dari session jika ada
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif


    // View switching functionality
    function switchView(viewType) {
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const gridViewBtn = document.getElementById('gridViewBtn');

        localStorage.setItem('fileManagerViewType', viewType);

        if (viewType === 'grid') {
            tableView.classList.add('d-none');
            gridView.classList.remove('d-none');
            tableViewBtn.classList.remove('active');
            gridViewBtn.classList.add('active');
        } else {
            gridView.classList.add('d-none');
            tableView.classList.remove('d-none');
            gridViewBtn.classList.remove('active');
            tableViewBtn.classList.add('active');
        }
    }

    // Initialize view type from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const savedViewType = localStorage.getItem('fileManagerViewType') || 'table';
        switchView(savedViewType);
    });

    // Handle item selection in grid view
    let lastSelectedItem = null;

    document.querySelectorAll('.grid-item').forEach(item => {
        item.addEventListener('click', (e) => {
            if (!e.target.closest('button')) {
                toggleItemSelection(item, e);
            }
        });

        item.addEventListener('dblclick', (e) => {
            const itemType = item.dataset.type;
            if (itemType === 'file') {
                // For files, open in new tab
                const filePath = item.dataset.path;
                window.open(`{{ Storage::url('') }}${filePath}`, '_blank');
            } else if (itemType === 'directory') {
                // For directories, navigate to the directory
                const directoryPath = item.dataset.path;
                window.location.href = `{{ route('app.file-manager.index') }}?path=${directoryPath}`;
            }
        });

        item.addEventListener('contextmenu', (e) => {
            showContextMenu(e, item);
        });
    });

    document.querySelector('#files').addEventListener('change', function(e) {
        const selectedFiles = document.querySelector('#selectedFiles');
        selectedFiles.innerHTML = '';

        Array.from(this.files).forEach(file => {
            selectedFiles.innerHTML += `
                <div class="selected-file">
                    <i class="mr-2 fas fa-file"></i>
                    ${file.name} (${formatFileSize(file.size)})
                </div>
            `;
        });
    });

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Upload handler
    document.querySelector('#uploadButton').addEventListener('click', function() {
        const form = document.querySelector('#uploadForm');
        const formData = new FormData(form);
        const files = document.querySelector('#files').files;

        if (files.length === 0) {
            toastr.error('Please select files to upload');
            return;
        }

        // Show progress bar
        const progressBar = document.querySelector('#uploadProgress');
        const progressBarInner = progressBar.querySelector('.progress-bar');
        const uploadStatus = document.querySelector('#uploadStatus');
        progressBar.style.display = 'block';

        // Disable upload button
        this.disabled = true;

        axios.post(form.action, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: progressEvent => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                progressBarInner.style.width = percentCompleted + '%';
                progressBarInner.textContent = percentCompleted + '%';
                uploadStatus.textContent = `Uploading... ${formatFileSize(progressEvent.loaded)} of ${formatFileSize(progressEvent.total)}`;
            }
        })
        .then(response => {
            if (response.data.success) {
                toastr.success(`Successfully uploaded ${response.data.uploaded.length} files`);
                if (response.data.errors.length > 0) {
                    response.data.errors.forEach(error => toastr.warning(error));
                }
                setTimeout(() => window.location.reload(), 1500);
            } else {
                toastr.error('Upload failed');
            }
        })
        .catch(error => {
            toastr.error(error.response?.data?.message || 'Error uploading files');
        })
        .finally(() => {
            // Reset form and hide progress
            form.reset();
            document.querySelector('#selectedFiles').innerHTML = '';
            progressBar.style.display = 'none';
            this.disabled = false;
        });
    });

    // Update file input label with selected files
    document.querySelector('#files').addEventListener('change', function(e) {
        const fileName = Array.from(this.files).map(file => file.name).join(', ');
        const label = document.querySelector('.custom-file-label');
        label.textContent = fileName.length > 50 ? fileName.substring(0, 47) + '...' : fileName;
    });

    // Update event listeners untuk grid items dan table rows
    function initializeItemListeners() {
        document.querySelectorAll('.file-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    toggleItemSelection(item, e);
                }
            });

            item.addEventListener('contextmenu', (e) => {
                showContextMenu(e, item);
            });
        });
    }

    // Tambahkan keyboard event listeners
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Shift') {
            isShiftKeyPressed = true;
        }
    });

    document.addEventListener('keyup', (e) => {
        if (e.key === 'Shift') {
            isShiftKeyPressed = false;
        }
    });

    // Initialize listeners when document is ready
    document.addEventListener('DOMContentLoaded', initializeItemListeners);
</script>
@endsection
