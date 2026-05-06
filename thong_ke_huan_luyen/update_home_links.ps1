$rootPath = "E:\Du_an_thong_ke\Thong_ke\thong_ke_huan_luyen\resources\views\backend"
$files = Get-ChildItem -Path $rootPath -Filter "*.blade.php" -Recurse

foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName)
    # Match <li class="nav-home"> followed by any whitespace and then an <a href="..."> pointing to soldiers.index
    $pattern = '(?s)(<li class="nav-home">\s*<a href=")\{\{ route\(''soldiers\.index''\) \}\}'
    $replacement = '$1{{ route(''dashboard'') }}'
    
    if ($content -match $pattern) {
        $newContent = $content -replace $pattern, $replacement
        [System.IO.File]::WriteAllText($file.FullName, $newContent)
        Write-Host "Updated: $($file.FullName)"
    }
}
