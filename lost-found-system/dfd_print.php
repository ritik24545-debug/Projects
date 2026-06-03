<?php
// dfd_print.php - Render DFD_Diagrams.txt as a printable page for PDF export
$root = __DIR__;
$txtPath = $root . DIRECTORY_SEPARATOR . 'DFD_Diagrams.txt';
$content = '';
$error = '';
if (file_exists($txtPath)) {
	$content = file_get_contents($txtPath);
	if ($content === false) {
		$error = 'Failed to read DFD_Diagrams.txt.';
	}
} else {
	$error = 'DFD_Diagrams.txt not found.';
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Lost & Found System - DFD (Printable)</title>
	<style>
		:root {
			--bg: #ffffff;
			--fg: #111111;
			--muted: #666666;
			--accent: #0b5ed7;
		}
		html, body {
			background: var(--bg);
			color: var(--fg);
			margin: 0;
			padding: 0;
			font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, "Apple Color Emoji", "Segoe UI Emoji";
		}
		.header {
			position: sticky;
			top: 0;
			background: var(--bg);
			border-bottom: 1px solid #e5e7eb;
			padding: 12px 16px;
			display: flex;
			align-items: center;
			gap: 12px;
			z-index: 10;
		}
		.header h1 {
			font-size: 18px;
			font-weight: 600;
			margin: 0;
		}
		.header .actions {
			margin-left: auto;
			display: flex;
			gap: 8px;
		}
		.button {
			appearance: none;
			border: 1px solid #d0d5dd;
			background: #f8fafc;
			color: var(--fg);
			padding: 8px 12px;
			border-radius: 8px;
			cursor: pointer;
			font-size: 14px;
		}
		.button.primary {
			background: var(--accent);
			border-color: var(--accent);
			color: #fff;
		}
		.container {
			max-width: 1024px;
			margin: 0 auto;
			padding: 16px;
		}
		.meta {
			color: var(--muted);
			font-size: 12px;
			margin-bottom: 8px;
		}
		pre {
			white-space: pre-wrap; /* wrap long lines for better PDF layout */
			word-wrap: break-word;
			font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
			font-size: 13px;
			line-height: 1.5;
			border: 1px solid #e5e7eb;
			border-radius: 8px;
			padding: 16px;
			background: #fcfcfd;
		}
		.error {
			color: #b42318;
			background: #fef3f2;
			border: 1px solid #fecdca;
			padding: 12px;
			border-radius: 8px;
		}
		@media print {
			.header { display: none; }
			.container { padding: 0; }
			pre { border: none; background: transparent; }
			/* Page size & margins for A4 portrait */
			@page { size: A4; margin: 16mm; }
		}
	</style>
</head>
<body>
	<div class="header">
		<h1>Lost & Found System — DFD (Printable)</h1>
		<div class="actions">
			<button class="button" onclick="window.location.reload()">Refresh</button>
			<button class="button primary" onclick="window.print()">Print to PDF</button>
		</div>
	</div>
	<div class="container">
		<div class="meta">Source: DFD_Diagrams.txt • Generated: <?php echo date('Y-m-d H:i'); ?></div>
		<?php if ($error): ?>
			<div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
		<?php else: ?>
			<pre><?php echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); ?></pre>
		<?php endif; ?>
	</div>
</body>
</html>
