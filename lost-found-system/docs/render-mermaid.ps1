Param(
  [string]$OutDir = "images",
  [ValidateSet("png","pdf","svg")]
  [string]$Format = "png"
)

if (-not (Get-Command mmdc -ErrorAction SilentlyContinue)) {
  Write-Host "Mermaid CLI (mmdc) not found. Install with: npm i -g @mermaid-js/mermaid-cli" -ForegroundColor Yellow
  exit 1
}

$here = Split-Path -Parent $MyInvocation.MyCommand.Path
$outPath = Join-Path $here $OutDir
New-Item -ItemType Directory -Force -Path $outPath | Out-Null

$files = @(
  "dfd_level0.mmd",
  "dfd_level1.mmd",
  "dfd_level2.mmd",
  "erd.mmd"
)

foreach ($f in $files) {
  $src = Join-Path $here $f
  $dst = Join-Path $outPath ( [IO.Path]::ChangeExtension($f, "." + $Format) )
  Write-Host "Rendering $f -> $dst"
  if ($Format -eq "pdf") {
    mmdc -i $src -o $dst --backgroundColor white --scale 1.5 --pdfFit
  } else {
    mmdc -i $src -o $dst --backgroundColor white --scale 1.5
  }
}

Write-Host "Done. Output files in $outPath" -ForegroundColor Green

