[CmdletBinding()]
param(
    [string]$PhpBin = "php",
    [string]$ComposerBin = "composer",
    [string]$NpmBin = "npm",
    [switch]$SkipNodeBuild,
    [switch]$SkipMigrate,
    [switch]$SeedBaselineData
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Invoke-Step {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Message,
        [Parameter(Mandatory = $true)]
        [scriptblock]$Action
    )

    Write-Host ""
    Write-Host "==> $Message" -ForegroundColor Cyan
    & $Action
}

function Invoke-External {
    param(
        [Parameter(Mandatory = $true)]
        [string]$FilePath,
        [string[]]$Arguments = @()
    )

    $commandLine = $FilePath

    if ($Arguments.Count -gt 0) {
        $commandLine += " " + ($Arguments -join " ")
    }

    Write-Host $commandLine -ForegroundColor DarkGray

    & $FilePath @Arguments

    if ($LASTEXITCODE -ne 0) {
        throw "Command failed: $commandLine"
    }
}

function Get-EnvValue {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Path,
        [Parameter(Mandatory = $true)]
        [string]$Key
    )

    if (-not (Test-Path -LiteralPath $Path)) {
        return $null
    }

    $pattern = "^\s*$([Regex]::Escape($Key))\s*=\s*(.*)$"

    foreach ($line in Get-Content -LiteralPath $Path) {
        if ($line -match $pattern) {
            return $Matches[1].Trim()
        }
    }

    return $null
}

function Test-EnvValueMissing {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Path,
        [Parameter(Mandatory = $true)]
        [string]$Key
    )

    $value = Get-EnvValue -Path $Path -Key $Key
    return [string]::IsNullOrWhiteSpace($value)
}

function Assert-CommandExists {
    param(
        [Parameter(Mandatory = $true)]
        [string]$Command
    )

    $null = Get-Command $Command -ErrorAction Stop
}

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..\..")).Path
$envPath = Join-Path $projectRoot ".env"
$envExamplePath = Join-Path $projectRoot ".env.example"
$publicStoragePath = Join-Path $projectRoot "public\storage"

Push-Location $projectRoot

try {
    if (-not (Test-Path -LiteralPath (Join-Path $projectRoot "artisan"))) {
        throw "Laravel artisan file was not found in $projectRoot"
    }

    Invoke-Step -Message "Checking required executables" -Action {
        Assert-CommandExists -Command $PhpBin
        Assert-CommandExists -Command $ComposerBin

        if (-not $SkipNodeBuild) {
            Assert-CommandExists -Command $NpmBin
        }
    }

    Invoke-Step -Message "Preparing environment file" -Action {
        if (-not (Test-Path -LiteralPath $envPath)) {
            if (-not (Test-Path -LiteralPath $envExamplePath)) {
                throw ".env.example was not found"
            }

            Copy-Item -LiteralPath $envExamplePath -Destination $envPath
            Write-Host "Created .env from .env.example" -ForegroundColor Green
        } else {
            Write-Host ".env already exists, keeping current values" -ForegroundColor Yellow
        }
    }

    Invoke-Step -Message "Installing Composer dependencies" -Action {
        Invoke-External -FilePath $ComposerBin -Arguments @(
            "install",
            "--no-dev",
            "--prefer-dist",
            "--optimize-autoloader",
            "--no-interaction"
        )
    }

    Invoke-Step -Message "Ensuring APP_KEY and JWT_SECRET are present" -Action {
        if (Test-EnvValueMissing -Path $envPath -Key "APP_KEY") {
            Invoke-External -FilePath $PhpBin -Arguments @("artisan", "key:generate", "--force")
        } else {
            Write-Host "APP_KEY already exists" -ForegroundColor Yellow
        }

        if (Test-EnvValueMissing -Path $envPath -Key "JWT_SECRET") {
            Invoke-External -FilePath $PhpBin -Arguments @("artisan", "jwt:secret", "--force")
        } else {
            Write-Host "JWT_SECRET already exists" -ForegroundColor Yellow
        }
    }

    Invoke-Step -Message "Running database migrations" -Action {
        if ($SkipMigrate) {
            Write-Host "Skipping migrations" -ForegroundColor Yellow
        } else {
            Invoke-External -FilePath $PhpBin -Arguments @("artisan", "migrate", "--force")
        }
    }

    Invoke-Step -Message "Seeding baseline lookup data" -Action {
        if (-not $SeedBaselineData) {
            Write-Host "Skipping baseline seeders" -ForegroundColor Yellow
            return
        }

        $seeders = @(
            "Database\\Seeders\\LocationSeeder",
            "Database\\Seeders\\CategorySeeder",
            "Database\\Seeders\\PostTypeSeeder",
            "Database\\Seeders\\PermissionSeeder",
            "Database\\Seeders\\RolePermissionSeeder"
        )

        foreach ($seeder in $seeders) {
            Invoke-External -FilePath $PhpBin -Arguments @("artisan", "db:seed", "--class=$seeder", "--force")
        }
    }

    Invoke-Step -Message "Ensuring storage link exists" -Action {
        if (Test-Path -LiteralPath $publicStoragePath) {
            Write-Host "public/storage already exists" -ForegroundColor Yellow
        } else {
            Invoke-External -FilePath $PhpBin -Arguments @("artisan", "storage:link")
        }
    }

    Invoke-Step -Message "Building frontend assets" -Action {
        if ($SkipNodeBuild) {
            Write-Host "Skipping Node.js build" -ForegroundColor Yellow
            return
        }

        Invoke-External -FilePath $NpmBin -Arguments @("ci")
        Invoke-External -FilePath $NpmBin -Arguments @("run", "build")
    }

    Invoke-Step -Message "Refreshing application caches" -Action {
        Invoke-External -FilePath $PhpBin -Arguments @("artisan", "config:clear")
        Invoke-External -FilePath $PhpBin -Arguments @("artisan", "cache:clear")
        Invoke-External -FilePath $PhpBin -Arguments @("artisan", "config:cache")
        Invoke-External -FilePath $PhpBin -Arguments @("artisan", "view:cache")
    }

    Write-Host ""
    Write-Host "Deployment bootstrap completed." -ForegroundColor Green
    Write-Host "IIS should point to the public directory: $($projectRoot)\public"
    Write-Host "Route cache is intentionally skipped because this project uses closure routes."
}
finally {
    Pop-Location
}
