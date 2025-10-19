<?php
// Set your uploads directory
$uploadDir = __DIR__ . '/uploads/';

// Create the directory if it does not exist
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Helper to create a unique filename
function make_unique_filename($original) {
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $base = pathinfo($original, PATHINFO_FILENAME);
    $stamp = date("Ymd_His");
    $rand = rand(1000,9999);
    return $base . '_' . $stamp . '_' . $rand . '.' . $ext;
}

// Support both single and multiple uploads
if (isset($_FILES['RemoteFile'])) {
    $files = $_FILES['RemoteFile'];
    $result = [];

    // If multiple files
    if (is_array($files['name'])) {
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $fileTmpPath = $files['tmp_name'][$i];
            $originalName = $files['name'][$i];
            $uniqueName = make_unique_filename($originalName);
            $destination = $uploadDir . $uniqueName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $result[] = [
                    'status' => 'success',
                    'saved_as' => $uniqueName
                ];
            } else {
                $result[] = [
                    'status' => 'fail',
                    'error' => 'Could not move file.'
                ];
            }
        }
        // Output JSON if multiple uploads
        header('Content-Type: application/json');
        echo json_encode($result);

    // If single file
    } else {
        $fileTmpPath = $files['tmp_name'];
        $originalName = $files['name'];
        $uniqueName = make_unique_filename($originalName);
        $destination = $uploadDir . $uniqueName;

        if (move_uploaded_file($fileTmpPath, $destination)) {
            echo 'Upload successful: ' . htmlspecialchars($uniqueName);
        } else {
            echo 'Upload failed: Could not move file.';
        }
    }
} else {
    echo 'No file uploaded. Field name should be "RemoteFile".';
}
?>