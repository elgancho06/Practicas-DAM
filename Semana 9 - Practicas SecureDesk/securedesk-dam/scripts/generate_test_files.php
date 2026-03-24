<?php
$filesDir = __DIR__;

// 1. PDF válido
file_put_contents($filesDir . '/archivo_valido.pdf', "%PDF-1.4\n%...\n%%EOF");

// 2. PNG válido (cabecera 89 50 4E 47 0D 0A 1A 0A)
file_put_contents($filesDir . '/archivo_valido.png', "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a" . str_repeat("\x00", 20));

// 3. TXT válido
file_put_contents($filesDir . '/archivo_valido.txt', "Contenido de texto plano.");

// 4. EXE prohibido (cabecera MZ: 4D 5A)
file_put_contents($filesDir . '/archivo_prohibido.exe', "\x4d\x5a\x90\x00\x03\x00\x00\x00");

// 5. JS prohibido (será detectado como text/plain si el contenido es simple, o application/javascript)
file_put_contents($filesDir . '/archivo_prohibido.js', "console.log('test');");

// 6. PDF Grande (>5MB)
$fp = fopen($filesDir . '/archivo_grande.pdf', 'w');
fwrite($fp, "%PDF-1.4\n");
fseek($fp, 5.1 * 1024 * 1024);
fwrite($fp, "%%EOF");
fclose($fp);

echo "Archivos de prueba generados correctamente.\n";
