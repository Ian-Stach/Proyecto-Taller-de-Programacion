param(
    [string]$InputPath = "database/seeders/data/products.json",
    [string]$OutputPath = "database/seeders/data/products.json"
)

$ErrorActionPreference = 'Stop'

function Get-PropertyValue {
    param(
        [object]$Object,
        [string]$Name
    )

    $property = $Object.PSObject.Properties[$Name]

    if ($null -eq $property) {
        return $null
    }

    return $property.Value
}

function Get-PrimaryCategoryPath {
    param([object]$Product)

    $categories = @(Get-PropertyValue -Object $Product -Name 'categories')

    if ($categories.Count -eq 0) {
        return $null
    }

    return [string]$categories[0]
}

function Get-LengthMetersFromDescription {
    param([AllowNull()][string]$Description)

    if ([string]::IsNullOrWhiteSpace($Description)) {
        return $null
    }

    $match = [regex]::Match($Description, '(?i)(\d+(?:\.\d+)?)\s*m de largo')

    if (-not $match.Success) {
        return $null
    }

    return [double]::Parse($match.Groups[1].Value, [System.Globalization.CultureInfo]::InvariantCulture)
}

function Get-ExistingHeightMeters {
    param([object]$Product)

    $height = Get-PropertyValue -Object $Product -Name 'height_meters'

    if ($null -eq $height -or $height -eq '') {
        return $null
    }

    return [double]$height
}

function Resolve-DietFromCategory {
    param([AllowNull()][string]$CategoryPath)

    if ([string]::IsNullOrWhiteSpace($CategoryPath)) {
        return $null
    }

    switch -Regex ($CategoryPath) {
        'Oviraptorosauria|Ornithomimosauria|Alvarezsauridae|Avialae' { return 'omnivoro' }
        'Therizinosauridae' { return 'herbivoro' }
        'Theropoda|Herrerasauridae|Coelophysoidea|Ceratosauria|Abelisauroidea|Abelisauridae|Megalosauroidea|Allosauroidea|Carcharodontosauridae|Tyrannosauroidea|Compsognathidae|Dromaeosauridae|Troodontidae' { return 'carnivoro' }
        default { return 'herbivoro' }
    }
}

function Resolve-Diet {
    param([object]$Product)

    $existingDiet = Get-PropertyValue -Object $Product -Name 'diet'

    if (-not [string]::IsNullOrWhiteSpace($existingDiet)) {
        return [string]$existingDiet
    }

    return Resolve-DietFromCategory (Get-PrimaryCategoryPath $Product)
}

function Resolve-Era {
    param([object]$Product)

    $existingEra = Get-PropertyValue -Object $Product -Name 'era'

    if (-not [string]::IsNullOrWhiteSpace($existingEra)) {
        return [string]$existingEra
    }

    $description = [string](Get-PropertyValue -Object $Product -Name 'description')

    switch -Regex ($description) {
        'triasico' { return 'triasico' }
        'jurasico' { return 'jurasico' }
        'cretacico' { return 'cretacico' }
    }

    $eraOverrides = @{
        'Hagryphus' = 'cretacico'
    }

    $name = [string](Get-PropertyValue -Object $Product -Name 'name')

    if ($eraOverrides.ContainsKey($name)) {
        return $eraOverrides[$name]
    }

    return $null
}

function Resolve-Habitat {
    param([object]$Product)

    $existingHabitat = Get-PropertyValue -Object $Product -Name 'habitat'

    if (-not [string]::IsNullOrWhiteSpace($existingHabitat)) {
        return [string]$existingHabitat
    }

    $categoryPath = Get-PrimaryCategoryPath $Product

    if ($categoryPath -match 'Avialae') {
        return 'volador'
    }

    return 'terrestre'
}

function Get-HeightRatio {
    param([AllowNull()][string]$CategoryPath)

    if ([string]::IsNullOrWhiteSpace($CategoryPath)) {
        return 0.28
    }

    switch -Regex ($CategoryPath) {
        'Sauropoda|Titanosauria|Brachiosauridae|Diplodocoidea|Macronaria' { return 0.45 }
        'Sauropodomorpha' { return 0.25 }
        'Ceratopsia' { return 0.30 }
        'Stegosauria|Ankylosauria|Thyreophora' { return 0.28 }
        'Hadrosauridae|Iguanodontia|Ornithopoda' { return 0.27 }
        'Pachycephalosauria|Marginocephalia|Heterodontosauridae' { return 0.25 }
        'Avialae' { return 0.18 }
        'Tyrannosauroidea|Carcharodontosauridae|Allosauroidea|Abelisauridae|Ceratosauria|Megalosauroidea' { return 0.34 }
        'Dromaeosauridae|Troodontidae|Ornithomimosauria|Alvarezsauridae|Oviraptorosauria|Compsognathidae|Coelurosauria' { return 0.22 }
        'Theropoda|Herrerasauridae|Coelophysoidea' { return 0.30 }
        default { return 0.28 }
    }
}

function Get-DefaultHeightMeters {
    param([AllowNull()][string]$CategoryPath)

    if ([string]::IsNullOrWhiteSpace($CategoryPath)) {
        return 2.0
    }

    switch -Regex ($CategoryPath) {
        'Sauropoda|Titanosauria|Brachiosauridae|Diplodocoidea|Macronaria' { return 7.5 }
        'Sauropodomorpha' { return 2.5 }
        'Ceratopsia' { return 3.0 }
        'Stegosauria|Ankylosauria|Thyreophora' { return 2.8 }
        'Hadrosauridae|Iguanodontia|Ornithopoda' { return 2.6 }
        'Pachycephalosauria|Marginocephalia|Heterodontosauridae' { return 2.0 }
        'Avialae' { return 0.6 }
        'Dromaeosauridae|Troodontidae|Ornithomimosauria|Alvarezsauridae|Oviraptorosauria|Compsognathidae|Coelurosauria' { return 1.2 }
        'Theropoda|Herrerasauridae|Coelophysoidea|Ceratosauria|Megalosauroidea|Allosauroidea|Tyrannosauroidea' { return 3.4 }
        default { return 2.0 }
    }
}

function Resolve-HeightMeters {
    param([object]$Product)

    $existingHeight = Get-ExistingHeightMeters $Product

    if ($null -ne $existingHeight) {
        return [math]::Round($existingHeight, 2)
    }

    $categoryPath = Get-PrimaryCategoryPath $Product
    $lengthMeters = Get-LengthMetersFromDescription ([string](Get-PropertyValue -Object $Product -Name 'description'))

    if ($null -ne $lengthMeters) {
        $estimatedHeight = [Math]::Max(0.4, ($lengthMeters * (Get-HeightRatio $categoryPath)))

        return [math]::Round($estimatedHeight, 2)
    }

    return [math]::Round((Get-DefaultHeightMeters $categoryPath), 2)
}

function Get-SizeBand {
    param(
        [AllowNull()][object]$LengthMeters,
        [double]$HeightMeters
    )

    $hasLength = $null -ne $LengthMeters -and [double]$LengthMeters -gt 0

    if ($hasLength) {
        $resolvedLength = [double]$LengthMeters

        if ($resolvedLength -ge 10.0) {
            return 'grande'
        }

        if ($resolvedLength -ge 4.0) {
            return 'mediano'
        }

        return 'pequeno'
    }

    if ($HeightMeters -ge 9.0) {
        return 'grande'
    }

    if ($HeightMeters -ge 4.0) {
        return 'mediano'
    }

    return 'pequeno'
}

function Get-CladePricePremium {
    param(
        [AllowNull()][string]$CategoryPath,
        [string]$Diet,
        [string]$SizeBand
    )

    if ([string]::IsNullOrWhiteSpace($CategoryPath)) {
        return 0
    }

    $premium = 0

    if ($Diet -eq 'carnivoro') {
        switch -Regex ($CategoryPath) {
            'Tyrannosauroidea|Carcharodontosauridae' { $premium += 190000 }
            'Allosauroidea|Abelisauridae|Ceratosauria|Megalosauroidea' { $premium += 145000 }
            'Dromaeosauridae|Troodontidae' { $premium += 110000 }
            'Oviraptorosauria|Alvarezsauridae|Ornithomimosauria' { $premium += 50000 }
            'Theropoda' { $premium += 70000 }
        }

        switch ($SizeBand) {
            'mediano' { $premium += 70000 }
            'grande' { $premium += 160000 }
        }
    }

    if ($Diet -eq 'herbivoro') {
        switch -Regex ($CategoryPath) {
            'Sauropoda|Titanosauria|Brachiosauridae|Diplodocoidea|Macronaria' { $premium += 130000 }
            'Ceratopsia|Ankylosauria|Stegosauria' { $premium += 80000 }
            'Hadrosauridae|Iguanodontia' { $premium += 45000 }
        }
    }

    if ($Diet -eq 'omnivoro' -and $CategoryPath -match 'Avialae|Oviraptorosauria|Alvarezsauridae') {
        $premium += 35000
    }

    return $premium
}

function Get-LengthPricePremium {
    param(
        [AllowNull()][object]$LengthMeters,
        [string]$Diet
    )

    if ($null -eq $LengthMeters -or [double]$LengthMeters -le 0) {
        return 0
    }

    $resolvedLength = [double]$LengthMeters
    $perMeter = switch ($Diet) {
        'carnivoro' { 28000 }
        'omnivoro' { 18000 }
        default { 16000 }
    }

    return [int]([math]::Round($resolvedLength * $perMeter, 0))
}

function Get-CladeStockAdjustment {
    param(
        [AllowNull()][string]$CategoryPath,
        [string]$Diet,
        [string]$SizeBand
    )

    if ([string]::IsNullOrWhiteSpace($CategoryPath)) {
        return 0
    }

    $adjustment = 0

    if ($Diet -eq 'carnivoro') {
        switch -Regex ($CategoryPath) {
            'Tyrannosauroidea|Carcharodontosauridae' { $adjustment -= 2 }
            'Allosauroidea|Abelisauridae|Ceratosauria|Megalosauroidea' { $adjustment -= 1 }
            'Dromaeosauridae|Troodontidae' { $adjustment -= 1 }
        }
    }

    if ($Diet -eq 'herbivoro') {
        switch -Regex ($CategoryPath) {
            'Sauropoda|Titanosauria|Brachiosauridae|Diplodocoidea|Macronaria' { $adjustment -= 2 }
            'Ceratopsia|Ankylosauria|Stegosauria' { $adjustment -= 1 }
        }
    }

    if ($Diet -eq 'omnivoro' -and $CategoryPath -match 'Avialae') {
        $adjustment += 1
    }

    if ($SizeBand -eq 'grande' -and $Diet -eq 'carnivoro') {
        $adjustment -= 1
    }

    return $adjustment
}

function Get-LengthStockAdjustment {
    param([AllowNull()][object]$LengthMeters)

    if ($null -eq $LengthMeters -or [double]$LengthMeters -le 0) {
        return 0
    }

    $resolvedLength = [double]$LengthMeters

    if ($resolvedLength -ge 12.0) {
        return -2
    }

    if ($resolvedLength -ge 8.0) {
        return -1
    }

    if ($resolvedLength -le 2.0) {
        return 1
    }

    return 0
}

function Get-IconicPricePremium {
    param([AllowNull()][string]$Name)

    if ([string]::IsNullOrWhiteSpace($Name)) {
        return 0
    }

    $iconicPremiums = @{
        'Tyrannosaurus' = 220000
        'Spinosaurus' = 170000
        'Velociraptor' = 150000
        'Triceratops' = 120000
        'Stegosaurus' = 100000
        'Brachiosaurus' = 130000
        'Diplodocus' = 110000
        'Allosaurus' = 100000
        'Ankylosaurus' = 95000
        'Parasaurolophus' = 85000
    }

    if ($iconicPremiums.ContainsKey($Name)) {
        return $iconicPremiums[$Name]
    }

    return 0
}

function Get-IconicStockAdjustment {
    param([AllowNull()][string]$Name)

    if ([string]::IsNullOrWhiteSpace($Name)) {
        return 0
    }

    $lowestStockIconics = @('Tyrannosaurus', 'Spinosaurus', 'Brachiosaurus', 'Diplodocus', 'Allosaurus')
    $reducedStockIconics = @('Velociraptor', 'Triceratops', 'Stegosaurus', 'Ankylosaurus', 'Parasaurolophus')

    if ($lowestStockIconics -contains $Name) {
        return -1
    }

    if ($reducedStockIconics -contains $Name) {
        return -1
    }

    return 0
}

function Resolve-Price {
    param(
        [AllowNull()][string]$Name,
        [string]$Diet,
        [string]$Habitat,
        [string]$SizeBand,
        [AllowNull()][string]$CategoryPath,
        [AllowNull()][object]$LengthMeters
    )

    $basePrice = switch ($SizeBand) {
        'pequeno' { 240000 }
        'mediano' { 470000 }
        'grande' { 840000 }
        default { 240000 }
    }

    $dietAdd = switch ($Diet) {
        'omnivoro' { 110000 }
        'carnivoro' { 250000 }
        default { 0 }
    }

    $habitatAdd = switch ($Habitat) {
        'terrestre' { 85000 }
        'volador' { 45000 }
        'acuatico' { 0 }
        default { 45000 }
    }

    $price = $basePrice +
        $dietAdd +
        $habitatAdd +
        (Get-CladePricePremium -CategoryPath $CategoryPath -Diet $Diet -SizeBand $SizeBand) +
        (Get-LengthPricePremium -LengthMeters $LengthMeters -Diet $Diet) +
        (Get-IconicPricePremium -Name $Name)

    $roundedPrice = [math]::Round($price / 5000, 0) * 5000

    return [decimal]::Round([decimal]$roundedPrice, 2)
}

function Resolve-Stock {
    param(
        [AllowNull()][string]$Name,
        [string]$Diet,
        [string]$Habitat,
        [string]$SizeBand,
        [AllowNull()][string]$CategoryPath,
        [AllowNull()][object]$LengthMeters
    )

    $baseStock = switch ($SizeBand) {
        'pequeno' { 10 }
        'mediano' { 6 }
        'grande' { 3 }
        default { 6 }
    }

    $dietAdjust = switch ($Diet) {
        'herbivoro' { 2 }
        'omnivoro' { 0 }
        'carnivoro' { -2 }
        default { 0 }
    }

    $habitatAdjust = switch ($Habitat) {
        'terrestre' { -1 }
        'volador' { 0 }
        'acuatico' { 1 }
        default { 0 }
    }

    $stock = $baseStock +
        $dietAdjust +
        $habitatAdjust +
        (Get-CladeStockAdjustment -CategoryPath $CategoryPath -Diet $Diet -SizeBand $SizeBand) +
        (Get-LengthStockAdjustment -LengthMeters $LengthMeters) +
        (Get-IconicStockAdjustment -Name $Name)

    if ($stock -lt 1) {
        return 1
    }

    if ($stock -gt 12) {
        return 12
    }

    return [int]$stock
}

$products = Get-Content $InputPath -Raw | ConvertFrom-Json
$enrichedProducts = New-Object System.Collections.Generic.List[object]

foreach ($product in $products) {
    $name = [string](Get-PropertyValue -Object $product -Name 'name')
    $categoryPath = Get-PrimaryCategoryPath $product
    $categories = @(Get-PropertyValue -Object $product -Name 'categories')
    $description = [string](Get-PropertyValue -Object $product -Name 'description')
    $image = Get-PropertyValue -Object $product -Name 'image'
    $diet = Resolve-Diet $product
    $era = Resolve-Era $product
    $habitat = Resolve-Habitat $product
    $heightMeters = Resolve-HeightMeters $product
    $lengthMeters = Get-LengthMetersFromDescription $description
    $sizeBand = Get-SizeBand -LengthMeters $lengthMeters -HeightMeters $heightMeters
    $price = Resolve-Price -Name $name -Diet $diet -Habitat $habitat -SizeBand $sizeBand -CategoryPath $categoryPath -LengthMeters $lengthMeters
    $stock = Resolve-Stock -Name $name -Diet $diet -Habitat $habitat -SizeBand $sizeBand -CategoryPath $categoryPath -LengthMeters $lengthMeters

    $orderedProduct = [ordered]@{
        categories = $categories
        name = $name
        description = $description
        price = $price
        stock = $stock
        image = $(if ([string]::IsNullOrWhiteSpace([string]$image)) { $null } else { [string]$image })
        active = $true
        height_meters = $heightMeters
        habitat = $habitat
        diet = $diet
        era = $era
    }

    $enrichedProducts.Add([pscustomobject]$orderedProduct)
}

$json = $enrichedProducts | ConvertTo-Json -Depth 6
[System.IO.File]::WriteAllText($OutputPath, $json, (New-Object System.Text.UTF8Encoding($false)))

Write-Output "Productos enriquecidos: $($enrichedProducts.Count)"