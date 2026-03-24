<?php
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg', 'image/png', 'image/gif',
    'application/pdf', 'text/plain',
    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

$testFiles = [
    'archivo_valido.pdf',
    'archivo_valido.png',
    'archivo_valido.txt',
    'archivo_prohibido.exe',
    'archivo_prohibido.js',
    'archivo_grande.pdf'
];

printf("| %-22s | %-20s | %-12s | %-12s |\n", "Archivo", "MIME Detectado", "¿Permitido?", "Resultado");
echo "|------------------------|----------------------|--------------|--------------|\n";

foreach ($testFiles as $filename) {
    $filePath = __DIR__ . '/' . $filename;
    $size = filesize($filePath);
    
    // Simulación de validación
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $filePath);
    finfo_close($finfo);

    $allowedByMime = in_array($mime, ALLOWED_MIME_TYPES);
    $allowedBySize = $size <= MAX_FILE_SIZE;

    $status = "Aceptado";
    if (!$allowedBySize) $status = "Rechazo (T)";
    elseif (!$allowedByMime) $status = "Rechazo (M)";

    printf("| %-22s | %-20s | %-12s | %-12s |\n", 
           $filename, $mime, ($allowedByMime && $allowedBySize ? "Sí" : "No"), $status);
}
