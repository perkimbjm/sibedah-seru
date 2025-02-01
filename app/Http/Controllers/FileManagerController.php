<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Laravel\Facades\Image;

class FileManagerController extends Controller
{
    public function index()
    {
        $path = request()->get('path', '');
        $directories = [];
        $files = [];

        // Get all files and directories in the current path
        $allFiles = Storage::disk('public')->files($path);
        $allDirectories = Storage::disk('public')->directories($path);

        // Process directories
        foreach ($allDirectories as $dir) {
            $directories[] = [
                'name' => basename($dir),
                'path' => $dir,
                'last_modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($dir))
            ];
        }

        // Process files
        foreach ($allFiles as $file) {
            if (basename($file)[0] === '.') {
                continue;
            }

            $files[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => $this->formatSize(Storage::disk('public')->size($file)),
                'extension' => pathinfo($file, PATHINFO_EXTENSION),
                'last_modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($file))
            ];
        }

        // Get breadcrumb data
        $breadcrumbs = [];
        $currentPath = '';
        $pathParts = array_filter(explode('/', $path));

        foreach ($pathParts as $part) {
            $currentPath .= $part . '/';
            $breadcrumbs[] = [
                'name' => $part,
                'path' => trim($currentPath, '/')
            ];
        }

        return view('file-manager.index', compact('directories', 'files', 'path', 'breadcrumbs'));
    }

    private function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file',
            'path' => 'nullable|string'
        ]);

        $path = $request->get('path', '');
        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $filePath = $path ? $path . '/' . $file->getClientOriginalName() : $file->getClientOriginalName();
                Storage::disk('public')->putFileAs($path, $file, $file->getClientOriginalName());
                $uploadedFiles[] = $file->getClientOriginalName();
            } catch (\Exception $e) {
                $errors[] = "Error uploading {$file->getClientOriginalName()}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => count($uploadedFiles) > 0,
            'uploaded' => $uploadedFiles,
            'errors' => $errors
        ]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'required|string',
            'path' => 'nullable|string'
        ]);

        $path = $request->get('path', '');
        $folderName = $request->get('folder_name');

        $folderPath = $path ? $path . '/' . $folderName : $folderName;

        Storage::disk('public')->makeDirectory($folderPath);

        return redirect()->route('app.file-manager.index', ['path' => $path])
            ->with('success', 'Folder created successfully');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'type' => 'required|in:file,directory'
        ]);

        try {
            $path = $request->path;
            $type = $request->type;

            if ($type === 'directory') {
                Storage::disk('public')->deleteDirectory($path);
            } else {
                Storage::disk('public')->delete($path);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rename(Request $request)
    {
        $request->validate([
            'old_path' => 'required|string',
            'new_name' => 'required|string'
        ]);

        $oldPath = $request->old_path;
        $newPath = dirname($oldPath) . '/' . $request->new_name;

        Storage::disk('public')->move($oldPath, $newPath);

        return response()->json(['message' => 'Item renamed successfully']);
    }

    public function copy(Request $request)
    {
        $request->validate([
            'source_path' => 'required|string',
            'destination_path' => 'required|string',
            'replace' => 'boolean'
        ]);

        $sourcePath = $request->source_path;
        $destPath = $request->destination_path;

        // Check if source exists
        if (!Storage::disk('public')->exists($sourcePath)) {
            return response()->json(['message' => 'Source file not found'], 404);
        }

        // Check if destination already exists
        if (Storage::disk('public')->exists($destPath) && !$request->replace) {
            return response()->json([
                'message' => 'File already exists',
                'needsConfirmation' => true
            ], 409);
        }

        try {
            Storage::disk('public')->copy($sourcePath, $destPath);
            return response()->json(['message' => 'Item copied successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error copying file: ' . $e->getMessage()], 500);
        }
    }

    public function move(Request $request)
    {
        $request->validate([
            'source_path' => 'required|string',
            'destination_path' => 'required|string',
            'replace' => 'boolean'
        ]);

        $sourcePath = $request->source_path;
        $destPath = $request->destination_path;

        // Check if source exists
        if (!Storage::disk('public')->exists($sourcePath)) {
            return response()->json(['message' => 'Source file not found'], 404);
        }

        // Check if destination already exists
        if (Storage::disk('public')->exists($destPath) && !$request->replace) {
            return response()->json([
                'message' => 'File already exists',
                'needsConfirmation' => true
            ], 409);
        }

        try {
            Storage::disk('public')->move($sourcePath, $destPath);
            return response()->json(['message' => 'Item moved successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error moving file: ' . $e->getMessage()], 500);
        }
    }

    public function extract(Request $request)
    {
        $request->validate([
            'zip_path' => 'required|string'
        ]);

        $zipPath = $request->zip_path;
        $extractPath = dirname($zipPath);

        if (Storage::disk('public')->exists($zipPath)) {
            $zip = new \ZipArchive;
            $fullPath = Storage::disk('public')->path($zipPath);

            if ($zip->open($fullPath) === TRUE) {
                $extractFullPath = Storage::disk('public')->path($extractPath);
                $zip->extractTo($extractFullPath);
                $zip->close();

                return response()->json(['message' => 'File extracted successfully']);
            }

            return response()->json(['message' => 'Failed to open zip file'], 500);
        }

        return response()->json(['message' => 'Zip file not found'], 404);
    }

    public function showThumbnail($path)
    {
        // Periksa apakah file ada di storage 'public'
        if (!Storage::disk('public')->exists($path)) {
            abort(404); // Return 404 jika file tidak ada
        }

        // Ambil path fisik file
        $fullPath = Storage::disk('public')->path($path);

        // Buat instance ImageManager dengan driver yang sesuai
        $manager = new ImageManager(new Driver());

        // Baca file dan buat thumbnail
        $image = $manager->read($fullPath)
                        ->scale(width: 100); // Resize lebar ke 100px, tinggi otomatis menyesuaikan

        // Encode gambar ke format WebP
        $encodedImage = $image->toWebp(75);

        // Return response gambar dengan header yang benar
        return response($encodedImage)->header('Content-Type', 'image/webp');
    }

    public function downloadFile(Request $request)
    {
        $path = $request->input('path');

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileName = basename($path);
        $fullPath = Storage::disk('public')->path($path);

        return response()->download($fullPath, $fileName);
    }

    public function downloadItems(Request $request)
    {
        $paths = $request->input('paths', []);

        // Jika direktori tunggal
        if (count($paths) === 1 && Storage::disk('public')->exists($paths[0])) {
            $path = $paths[0];

            // Cek apakah ini direktori
            if (Storage::disk('public')->exists($path)) {
                $zipName = basename($path) . '.zip';
                $zipPath = storage_path('app/temp/' . $zipName);

                // Buat direktori temp jika belum ada
                if (!File::exists(storage_path('app/temp'))) {
                    File::makeDirectory(storage_path('app/temp'), 0755, true);
                }

                // Hapus file zip lama jika ada
                if (File::exists($zipPath)) {
                    File::delete($zipPath);
                }

                $zip = new \ZipArchive();
                if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                    $basePath = Storage::disk('public')->path('');

                    // Jika ini direktori
                    if (Storage::disk('public')->exists($path)) {
                        // Tambahkan semua file dalam direktori
                        $files = Storage::disk('public')->allFiles($path);
                        foreach ($files as $file) {
                            $filePath = Storage::disk('public')->path($file);
                            // Dapatkan relative path dari file
                            $relativePath = substr($file, strlen($path) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }
                    }

                    $zip->close();
                    return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
                }
            }
        }

        // Jika multiple files
        $zipName = 'selected_files.zip';
        $zipPath = storage_path('app/temp/' . $zipName);

        // Buat direktori temp jika belum ada
        if (!File::exists(storage_path('app/temp'))) {
            File::makeDirectory(storage_path('app/temp'), 0755, true);
        }

        // Hapus file zip lama jika ada
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            foreach ($paths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    $filePath = Storage::disk('public')->path($path);
                    // Gunakan basename untuk nama file dalam zip
                    $zip->addFile($filePath, basename($path));
                }
            }

            $zip->close();
            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'Could not create zip file'], 500);
    }
}
