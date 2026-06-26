<#
.SYNOPSIS
    Links the LocalWP child-theme folder to this repository's canonical copy.

.DESCRIPTION
    Travel Without Borders local development workflow.

    The ONLY physical copy of the Ave Child theme lives in this repository at:
        07_Source\Themes\ave-child

    LocalWP consumes it through a Windows directory JUNCTION at:
        <LocalWP site>\app\public\wp-content\themes\ave-child   ->   repo copy

    Run this script to (re)create that junction. It is safe to run repeatedly.
    Typical reasons to run it:
      * after cloning the repo onto a new machine,
      * if LocalWP recreated a real 'ave-child' folder,
      * if the link was deleted.

    A directory junction is used (not a symlink) because junctions need no
    administrator rights and no Windows "Developer Mode", and both paths live
    on the same NTFS volume (C:).

.NOTES
    Does NOT touch the live website, production, or the database.
#>

[CmdletBinding()]
param(
    # Override only if your LocalWP site lives somewhere else.
    [string]$LocalThemePath = "C:\Users\pskan\Local Sites\travelwithoutborders\app\public\wp-content\themes\ave-child"
)

$ErrorActionPreference = 'Stop'

# Repo canonical copy is resolved relative to this script (repo\scripts\..).
$RepoThemePath = Join-Path $PSScriptRoot "..\07_Source\Themes\ave-child" | Resolve-Path | Select-Object -ExpandProperty Path

Write-Host "Repo canonical : $RepoThemePath"
Write-Host "LocalWP target : $LocalThemePath"

if (-not (Test-Path $RepoThemePath)) {
    throw "Repo child theme not found at $RepoThemePath. Are you running from inside the repo?"
}

# If something already exists at the LocalWP path, inspect it.
if (Test-Path $LocalThemePath) {
    $item = Get-Item $LocalThemePath -Force
    $isLink = ($item.Attributes -band [IO.FileAttributes]::ReparsePoint) -ne 0

    if ($isLink) {
        if ($item.Target -eq $RepoThemePath) {
            Write-Host "OK: Junction already exists and points to the repo. Nothing to do." -ForegroundColor Green
            return
        }
        Write-Host "Existing link points elsewhere ($($item.Target)); recreating..." -ForegroundColor Yellow
        cmd /c rmdir "$LocalThemePath" | Out-Null
    }
    else {
        # A REAL folder is here. Back it up before replacing, never delete blindly.
        $stamp  = Get-Date -Format "yyyyMMdd-HHmmss"
        $backup = Join-Path (Split-Path $LocalThemePath -Parent) "ave-child.backup-$stamp"
        Write-Host "A real folder exists. Backing it up to: $backup" -ForegroundColor Yellow
        Move-Item $LocalThemePath $backup
    }
}

cmd /c mklink /J "$LocalThemePath" "$RepoThemePath" | Out-Null

$new = Get-Item $LocalThemePath -Force
if ((($new.Attributes -band [IO.FileAttributes]::ReparsePoint) -ne 0) -and ($new.Target -eq $RepoThemePath)) {
    Write-Host "SUCCESS: Junction created. LocalWP now consumes the repo child theme." -ForegroundColor Green
}
else {
    throw "Junction verification failed."
}
