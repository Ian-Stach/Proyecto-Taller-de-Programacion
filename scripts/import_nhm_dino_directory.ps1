param(
    [string]$OutputPath = "database/seeders/data/products.json",
    [string]$IndexUrl = "https://www.nhm.ac.uk/discover/dino-directory/name/name-az-all/gallery.html",
    [int]$Limit = 0,
    [string[]]$IncludeSlugs = @(),
    [switch]$MergeExisting
)

$ErrorActionPreference = 'Stop'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

function Normalize-Whitespace {
    param([AllowNull()][string]$Text)

    if ([string]::IsNullOrWhiteSpace($Text)) {
        return $null
    }

    $normalized = [System.Web.HttpUtility]::HtmlDecode($Text)
    $normalized = $normalized -replace '<!--\s*-->', ''
    $normalized = $normalized -replace '<[^>]+>', ' '
    $normalized = $normalized -replace '\s+', ' '
    $normalized = $normalized.Trim()

    if ($normalized -eq '') {
        return $null
    }

    return $normalized
}

function Get-DtDdValue {
    param(
        [string]$Html,
        [string]$Label
    )

    $pattern = '(?s)<dt>' + [regex]::Escape($Label) + '</dt>\s*<dd[^>]*>(.*?)</dd>'
    $match = [regex]::Match($Html, $pattern)

    if (-not $match.Success) {
        return $null
    }

    return Normalize-Whitespace $match.Groups[1].Value
}

function Normalize-MeasurementValue {
    param([AllowNull()][string]$Value)

    if ([string]::IsNullOrWhiteSpace($Value)) {
        return $null
    }

    $normalized = $Value.Trim()
    $normalized = $normalized -replace '\s*m$', ''
    $normalized = $normalized -replace '\s*kg$', ''

    return $normalized.Trim()
}

function Get-FoundInValues {
    param([string]$Html)

    $match = [regex]::Match($Html, '(?s)<dt>Found in:</dt>(.*?)</dl>')

    if (-not $match.Success) {
        return @()
    }

    $countries = [regex]::Matches($match.Groups[1].Value, '>([^<]+)</a>') |
        ForEach-Object { Normalize-Whitespace $_.Groups[1].Value } |
        Where-Object { $_ }

    return @($countries | Select-Object -Unique)
}

function Get-PageTitle {
    param([string]$Html)

    $match = [regex]::Match($Html, '(?s)<h1[^>]*>(.*?)</h1>')

    if (-not $match.Success) {
        return $null
    }

    return Normalize-Whitespace $match.Groups[1].Value
}

function ConvertTo-CategoryPathMap {
    param([string]$Json)

    $map = @{}
    $decoded = $Json | ConvertFrom-Json

    foreach ($property in $decoded.PSObject.Properties) {
        $map[$property.Name] = [string]$property.Value
    }

    return $map
}

function Get-CategoryPathMap {
    $phpCode = @'
<?php

$catalogPath = getcwd() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeders' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'catalog.php';
$catalogData = require $catalogPath;
$result = [];
$walk = function(array $nodes, string $prefix = '') use (&$walk, &$result) {
    foreach ($nodes as $node) {
        $path = $prefix === '' ? $node['name'] : $prefix . ' > ' . $node['name'];
        $result[$node['name']] = $path;

        if (!empty($node['children']) && is_array($node['children'])) {
            $walk($node['children'], $path);
        }
    }
};
$walk($catalogData['categories'] ?? []);
echo json_encode($result, JSON_UNESCAPED_SLASHES);
'@

    $tempPhpFile = [System.IO.Path]::GetTempFileName() + '.php'

    try {
        Set-Content -Path $tempPhpFile -Value $phpCode -Encoding utf8
        $json = & php $tempPhpFile
    }
    finally {
        if (Test-Path $tempPhpFile) {
            Remove-Item $tempPhpFile -Force
        }
    }

    if ($LASTEXITCODE -ne 0 -or [string]::IsNullOrWhiteSpace($json)) {
        throw 'No se pudo cargar el mapa de categorias desde catalog.php.'
    }

    return ConvertTo-CategoryPathMap $json
}

function Get-TypeFallbackMappings {
    return @(
        @{ Pattern = 'prosauropod'; CategoryName = 'Sauropodomorpha' },
        @{ Pattern = 'sauropod'; CategoryName = 'Sauropoda' },
        @{ Pattern = 'titanosaur'; CategoryName = 'Titanosauria' },
        @{ Pattern = 'brachiosaur'; CategoryName = 'Brachiosauridae' },
        @{ Pattern = 'diplodocid'; CategoryName = 'Diplodocoidea' },
        @{ Pattern = 'large theropod'; CategoryName = 'Theropoda' },
        @{ Pattern = 'small theropod'; CategoryName = 'Theropoda' },
        @{ Pattern = 'theropod'; CategoryName = 'Theropoda' },
        @{ Pattern = 'tyrannosaur'; CategoryName = 'Tyrannosauroidea' },
        @{ Pattern = 'dromaeosaur'; CategoryName = 'Dromaeosauridae' },
        @{ Pattern = 'troodontid'; CategoryName = 'Troodontidae' },
        @{ Pattern = 'ornithomimid'; CategoryName = 'Ornithomimosauria' },
        @{ Pattern = 'oviraptorosaur'; CategoryName = 'Oviraptorosauria' },
        @{ Pattern = 'therizinosaur'; CategoryName = 'Therizinosauridae' },
        @{ Pattern = 'alvarezsaur'; CategoryName = 'Alvarezsauridae' },
        @{ Pattern = 'coelophysoid'; CategoryName = 'Coelophysoidea' },
        @{ Pattern = 'ceratosaur'; CategoryName = 'Ceratosauria' },
        @{ Pattern = 'abelisaur'; CategoryName = 'Abelisauridae' },
        @{ Pattern = 'allosaur'; CategoryName = 'Allosauroidea' },
        @{ Pattern = 'carcharodontosaur'; CategoryName = 'Carcharodontosauridae' },
        @{ Pattern = 'armoured dinosaur'; CategoryName = 'Thyreophora' },
        @{ Pattern = 'stegosaur'; CategoryName = 'Stegosauria' },
        @{ Pattern = 'ankylosaur'; CategoryName = 'Ankylosauria' },
        @{ Pattern = 'ceratopsian'; CategoryName = 'Ceratopsia' },
        @{ Pattern = 'ornithopod'; CategoryName = 'Ornithopoda' },
        @{ Pattern = 'hadrosaur'; CategoryName = 'Hadrosauridae' },
        @{ Pattern = 'iguanodont'; CategoryName = 'Iguanodontia' },
        @{ Pattern = 'pachycephalosaur'; CategoryName = 'Pachycephalosauria' },
        @{ Pattern = 'dome-headed dinosaur'; CategoryName = 'Pachycephalosauria' },
        @{ Pattern = 'heterodontosaur'; CategoryName = 'Heterodontosauridae' },
        @{ Pattern = 'bird'; CategoryName = 'Avialae' }
    )
}

function Resolve-CategoryPath {
    param(
        [AllowNull()][string]$Taxonomy,
        [AllowNull()][string]$Type,
        [hashtable]$CategoryPathMap,
        [array]$CategoryNamesByDepth,
        [array]$TypeFallbackMappings
    )

    $taxonomyTokens = @()

    if (-not [string]::IsNullOrWhiteSpace($Taxonomy)) {
        $taxonomyTokens = $Taxonomy.Split(',') |
            ForEach-Object { $_.Trim() } |
            Where-Object { $_ }
    }

    $taxonomyPath = $null

    foreach ($categoryName in $CategoryNamesByDepth) {
        if ($taxonomyTokens -contains $categoryName) {
            $taxonomyPath = $CategoryPathMap[$categoryName]
            break
        }
    }

    $typeFallbackPath = $null

    foreach ($mapping in $TypeFallbackMappings) {
        if ($Type -and $Type -match [regex]::Escape($mapping.Pattern) -and $CategoryPathMap.ContainsKey($mapping.CategoryName)) {
            $typeFallbackPath = $CategoryPathMap[$mapping.CategoryName]
            break
        }
    }

    if ($taxonomyPath -and $typeFallbackPath) {
        $taxonomyDepth = ($taxonomyPath -split ' > ').Count
        $typeDepth = ($typeFallbackPath -split ' > ').Count

        if ($typeDepth -gt $taxonomyDepth) {
            return $typeFallbackPath
        }

        return $taxonomyPath
    }

    if ($typeFallbackPath) {
        return $typeFallbackPath
    }

    if ($taxonomyPath) {
        return $taxonomyPath
    }

    return $null
}

function Convert-DietToAppValue {
    param([AllowNull()][string]$Diet)

    switch -Regex ($Diet) {
        'herbivorous' { return 'herbivoro' }
        'carnivorous' { return 'carnivoro' }
        'omnivorous' { return 'omnivoro' }
        default { return $null }
    }
}

function Convert-WhenLivedToEra {
    param([AllowNull()][string]$WhenLived)

    switch -Regex ($WhenLived) {
        'Triassic' { return 'triasico' }
        'Jurassic' { return 'jurasico' }
        'Cretaceous' { return 'cretacico' }
        default { return $null }
    }
}

function Convert-PeriodToSpanish {
    param([AllowNull()][string]$WhenLived)

    if ([string]::IsNullOrWhiteSpace($WhenLived)) {
        return $null
    }

    $period = $WhenLived.Split(',')[0].Trim()

    $specificPeriods = @{
        'Early Triassic' = 'Triasico temprano'
        'Middle Triassic' = 'Triasico medio'
        'Mid Triassic' = 'Triasico medio'
        'Late Triassic' = 'Triasico tardio'
        'Early Jurassic' = 'Jurasico temprano'
        'Middle Jurassic' = 'Jurasico medio'
        'Mid Jurassic' = 'Jurasico medio'
        'Late Jurassic' = 'Jurasico tardio'
        'Early Cretaceous' = 'Cretacico temprano'
        'Middle Cretaceous' = 'Cretacico medio'
        'Mid Cretaceous' = 'Cretacico medio'
        'Late Cretaceous' = 'Cretacico tardio'
    }

    if ($specificPeriods.ContainsKey($period)) {
        return $specificPeriods[$period]
    }

    $period = $period -replace 'Triassic', 'Triasico'
    $period = $period -replace 'Jurassic', 'Jurasico'
    $period = $period -replace 'Cretaceous', 'Cretacico'

    return $period.Trim()
}

function Get-PageText {
    param([string]$Html)

    return Normalize-Whitespace $Html
}

function Resolve-Habitat {
    param(
        [AllowNull()][string]$HowMoved,
        [AllowNull()][string]$PageText
    )

    if ($PageText -and $PageText -match '(?i)semi-aquatic|lived a semi-aquatic lifestyle|lived in and around water|spent a lot of its time living in and around water') {
        return 'acuatico'
    }

    if ($PageText -and $PageText -match '(?i)could fly|capable of flight|powered flight|flew') {
        return 'volador'
    }

    if ($HowMoved -and $HowMoved -match 'on 2 legs|on 4 legs|mostly on 2 legs|mostly on 4 legs') {
        return 'terrestre'
    }

    return $null
}

function Resolve-HeightMeters {
    param([AllowNull()][string]$PageText)

    if (-not $PageText) {
        return $null
    }

    $patterns = @(
        '(?i)height around (\d+(?:\.\d+)?) metres',
        '(?i)height of around (\d+(?:\.\d+)?) metres',
        '(?i)would make .*?height around (\d+(?:\.\d+)?) metres',
        '(?i)stood (\d+(?:\.\d+)?) metres tall at the hips'
    )

    foreach ($pattern in $patterns) {
        $match = [regex]::Match($PageText, $pattern)

        if ($match.Success) {
            return [double]::Parse($match.Groups[1].Value, [System.Globalization.CultureInfo]::InvariantCulture)
        }
    }

    return $null
}

function Join-Items {
    param([array]$Items)

    if (-not $Items -or $Items.Count -eq 0) {
        return $null
    }

    if ($Items.Count -eq 1) {
        return $Items[0]
    }

    if ($Items.Count -eq 2) {
        return "$($Items[0]) y $($Items[1])"
    }

    return (($Items[0..($Items.Count - 2)] -join ', ') + " y $($Items[-1])")
}

function Build-Description {
    param(
        [AllowNull()][string]$Diet,
        [AllowNull()][string]$PeriodSpanish,
        [array]$FoundIn,
        [AllowNull()][string]$Length,
        [AllowNull()][string]$Weight,
        [AllowNull()][string]$Type
    )

    $intro = 'Dinosaurio prehistorico'

    if ($Diet -and $PeriodSpanish) {
        $intro = "Dinosaurio $Diet del $($PeriodSpanish.ToLower())"
    } elseif ($Diet) {
        $intro = "Dinosaurio $Diet"
    } elseif ($PeriodSpanish) {
        $intro = "Dinosaurio del $($PeriodSpanish.ToLower())"
    }

    if ($FoundIn.Count -gt 0) {
        $intro += ' hallado en ' + (Join-Items $FoundIn)
    }

    $sentences = @($intro + '.')
    $sizeBits = @()

    if ($Length) {
        $sizeBits += "$Length m de largo"
    }

    if ($Weight) {
        $sizeBits += "$Weight kg de peso"
    }

    if ($sizeBits.Count -gt 0) {
        $sentences += 'Alcanzaba aproximadamente ' + (Join-Items $sizeBits) + '.'
    }

    if ($Type) {
        $sentences += "La ficha del NHM lo clasifica como $Type."
    }

    return ($sentences -join ' ')
}

function Get-ImageUrl {
    param(
        [string]$Html,
        [string]$Slug
    )

    $pattern = '(?i)/discover/dino-directory/_next/image\?url=([^"&]+(?:' + [regex]::Escape($Slug) + ')[^"&]*)&amp;w='
    $match = [regex]::Match($Html, $pattern)

    if (-not $match.Success) {
        return $null
    }

    $encodedUrl = [System.Web.HttpUtility]::HtmlDecode($match.Groups[1].Value)

    return 'https://www.nhm.ac.uk/discover/dino-directory/_next/image?url=' + $encodedUrl + '&w=1200&q=75'
}

function Get-NhmLinks {
    param([string]$Html)

    $links = [regex]::Matches($Html, '/discover/dino-directory/[a-z0-9-]+\.html') |
        ForEach-Object { $_.Value } |
        Sort-Object -Unique

    return @($links)
}

function Merge-WithExistingProducts {
    param(
        [System.Collections.Generic.List[object]]$ImportedProducts,
        [string]$Path
    )

    $mergedByName = @{}

    if (Test-Path $Path) {
        $existingProducts = Get-Content $Path -Raw | ConvertFrom-Json

        foreach ($existingProduct in @($existingProducts)) {
            $mergedByName[$existingProduct.name] = $existingProduct
        }
    }

    foreach ($importedProduct in $ImportedProducts) {
        $mergedByName[$importedProduct.name] = $importedProduct
    }

    $mergedProducts = New-Object System.Collections.Generic.List[object]

    foreach ($name in ($mergedByName.Keys | Sort-Object)) {
        $mergedProducts.Add($mergedByName[$name])
    }

    return $mergedProducts
}

$categoryPathMap = Get-CategoryPathMap
$categoryNamesByDepth = $categoryPathMap.Keys | Sort-Object { ($categoryPathMap[$_] -split ' > ').Count } -Descending
$typeFallbackMappings = Get-TypeFallbackMappings

$galleryHtml = (Invoke-WebRequest -UseBasicParsing $IndexUrl).Content
$relativeLinks = Get-NhmLinks $galleryHtml

if ($IncludeSlugs.Count -gt 0) {
    $slugSet = @{}

    foreach ($slug in $IncludeSlugs) {
        if (-not [string]::IsNullOrWhiteSpace($slug)) {
            $slugSet[$slug.Trim().ToLowerInvariant()] = $true
        }
    }

    $relativeLinks = $relativeLinks | Where-Object {
        $slugSet.ContainsKey([System.IO.Path]::GetFileNameWithoutExtension($_).ToLowerInvariant())
    }
}

if ($Limit -gt 0) {
    $relativeLinks = $relativeLinks | Select-Object -First $Limit
}

$products = New-Object System.Collections.Generic.List[object]
$skipped = New-Object System.Collections.Generic.List[string]

foreach ($relativeLink in $relativeLinks) {
    $url = 'https://www.nhm.ac.uk' + $relativeLink
    $slug = [System.IO.Path]::GetFileNameWithoutExtension($relativeLink)

    try {
        $html = (Invoke-WebRequest -UseBasicParsing $url).Content
        $pageText = Get-PageText $html
        $name = Get-PageTitle $html
        $dietRaw = Get-DtDdValue -Html $html -Label 'Diet:'
        $type = Get-DtDdValue -Html $html -Label 'Type of dinosaur:'
        $length = Normalize-MeasurementValue (Get-DtDdValue -Html $html -Label 'Length:')
        $weight = Normalize-MeasurementValue (Get-DtDdValue -Html $html -Label 'Weight:')
        $howMoved = Get-DtDdValue -Html $html -Label 'How it moved:'
        $whenLived = Get-DtDdValue -Html $html -Label 'When it lived:'
        $taxonomy = Get-DtDdValue -Html $html -Label 'Taxonomy:'
        $foundIn = Get-FoundInValues $html
        $diet = Convert-DietToAppValue $dietRaw
        $era = Convert-WhenLivedToEra $whenLived
        $periodSpanish = Convert-PeriodToSpanish $whenLived
        $habitat = Resolve-Habitat -HowMoved $howMoved -PageText $pageText
        $heightMeters = Resolve-HeightMeters $pageText
        $categoryPath = Resolve-CategoryPath -Taxonomy $taxonomy -Type $type -CategoryPathMap $categoryPathMap -CategoryNamesByDepth $categoryNamesByDepth -TypeFallbackMappings $typeFallbackMappings
        $imageUrl = Get-ImageUrl -Html $html -Slug $slug

        if (-not $name) {
            throw "No se pudo extraer el nombre desde [$url]."
        }

        if (-not $categoryPath) {
            $skipped.Add("$name|sin_categoria")
            continue
        }

        $product = [ordered]@{
            categories = @($categoryPath)
            name = $name
            description = Build-Description -Diet $diet -PeriodSpanish $periodSpanish -FoundIn $foundIn -Length $length -Weight $weight -Type $type
        }

        if ($imageUrl) {
            $product.image = $imageUrl
        }

        if ($heightMeters -ne $null) {
            $product.height_meters = [math]::Round($heightMeters, 2)
        }

        if ($habitat) {
            $product.habitat = $habitat
        }

        if ($diet) {
            $product.diet = $diet
        }

        if ($era) {
            $product.era = $era
        }

        $products.Add([pscustomobject]$product)
        Write-Output "OK  $name"
    }
    catch {
        $skipped.Add("$slug|$($_.Exception.Message)")
        Write-Warning "No se pudo procesar [$slug]: $($_.Exception.Message)"
    }
}

$json = $products | ConvertTo-Json -Depth 6
$outputDir = Split-Path -Parent $OutputPath

if ($outputDir) {
    New-Item -ItemType Directory -Force -Path $outputDir | Out-Null
}

if ($MergeExisting) {
    $products = Merge-WithExistingProducts -ImportedProducts $products -Path $OutputPath
    $json = $products | ConvertTo-Json -Depth 6
}

[System.IO.File]::WriteAllText($OutputPath, $json, (New-Object System.Text.UTF8Encoding($false)))
Write-Output "Escritos: $($products.Count)"
Write-Output "Saltados: $($skipped.Count)"

if ($skipped.Count -gt 0) {
    Write-Output 'Primeros saltados:'
    $skipped | Select-Object -First 20 | ForEach-Object { Write-Output $_ }
}