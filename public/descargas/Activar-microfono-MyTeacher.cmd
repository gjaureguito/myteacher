<# : MyTeacher - Activar microfono (archivo .cmd con PowerShell incluido)
@echo off
setlocal
title Activar microfono - MyTeacher
set "MT_SELF=%~f0"
powershell -NoProfile -ExecutionPolicy Bypass -Command "iex ((Get-Content -LiteralPath $env:MT_SELF -Raw -Encoding UTF8) -split ('#'+'>'),2)[1]"
exit /b
#>
# ---------------------------------------------------------------------------------------------
# MyTeacher - Activar micrófono para cualquier navegador (Chrome, Edge, Firefox, Opera, Brave…)
#
# Qué hace (y nada más):
#   1. Usuario actual: activa "Acceso al micrófono" y "Permitir que las aplicaciones de
#      escritorio accedan al micrófono", y saca el bloqueo a los navegadores que Windows
#      tenga marcados como denegados.
#   2. Avisa qué programas están usando el micrófono ahora mismo y ofrece cerrarlos.
#   3. Con permiso de administrador (opcional): activa el micrófono para todo el equipo,
#      vuelve a habilitar micrófonos deshabilitados y reinicia el servicio de audio.
#   4. Detecta el antivirus y explica cómo permitir el navegador (no lo modifica).
# Deja un registro en %TEMP%\myteacher-microfono.txt
# Compatible con Windows PowerShell 5.1 (incluido en Windows 10 y 11).
# ---------------------------------------------------------------------------------------------
$ErrorActionPreference = 'SilentlyContinue'
Add-Type -AssemblyName System.Windows.Forms
[System.Windows.Forms.Application]::EnableVisualStyles()

$Title = 'MyTeacher - Activar micrófono'
$Log = Join-Path $env:TEMP 'myteacher-microfono.txt'
"MyTeacher - Activar micrófono  $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')" | Set-Content -Path $Log -Encoding UTF8
function Write-Log([string]$text) { Add-Content -Path $Log -Value $text -Encoding UTF8 }
function Show-Box([string]$text, [string]$buttons = 'OK', [string]$icon = 'Information') {
    return [System.Windows.Forms.MessageBox]::Show($text, $Title, $buttons, $icon).ToString()
}

$BrowserPattern = '(?i)\\(chrome|msedge|firefox|opera|opera_gx|brave|vivaldi|waterfox|librewolf|yandex|arc|iexplore)\.exe$'
$done = New-Object System.Collections.Generic.List[string]
$todo = New-Object System.Collections.Generic.List[string]

if ([Environment]::OSVersion.Platform -ne 'Win32NT') { Write-Output 'Este asistente es solo para Windows.'; exit }

$answer = Show-Box ("Este asistente deja el micrófono listo para practicar en MyTeacher con cualquier navegador " +
    "(Chrome, Edge, Firefox, Opera, Brave…).`n`n" +
    "Solo cambia los permisos del micrófono. No toca tus archivos ni otros programas.`n`n" +
    "Antes de seguir, guardá lo que estés haciendo en el navegador: al final te vamos a pedir que lo cierres y lo vuelvas a abrir.`n`n¿Continuamos?") 'YesNo' 'Question'
if ($answer -ne 'Yes') { exit }

# ---------- 1. Permisos de Windows para el usuario actual ----------
$Consent = 'HKCU:\Software\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone'
function Set-Allow([string]$path) {
    if (-not (Test-Path -LiteralPath $path)) { New-Item -Path $path -Force | Out-Null }
    $old = (Get-ItemProperty -LiteralPath $path -Name Value).Value
    if ($old -ne 'Allow') {
        Set-ItemProperty -LiteralPath $path -Name Value -Value 'Allow' -Type String
        Write-Log "Permitido: $path (antes: $old)"
        return $true
    }
    return $false
}
if (Set-Allow $Consent) { $done.Add('Activé «Acceso al micrófono» de Windows.') }
if (Set-Allow "$Consent\NonPackaged") { $done.Add('Activé «Permitir que las aplicaciones de escritorio accedan al micrófono».') }

$unblocked = @()
Get-ChildItem -LiteralPath "$Consent\NonPackaged" | ForEach-Object {
    $exe = $_.PSChildName -replace '#', '\'
    if ($exe -match $BrowserPattern) {
        $value = (Get-ItemProperty -LiteralPath $_.PSPath -Name Value).Value
        if ($value -and $value -ne 'Allow') {
            Set-ItemProperty -LiteralPath $_.PSPath -Name Value -Value 'Allow' -Type String
            $unblocked += [IO.Path]::GetFileNameWithoutExtension($exe)
            Write-Log "Navegador desbloqueado: $exe"
        }
    }
}
if ($unblocked.Count -gt 0) { $done.Add('Desbloqueé el micrófono para: ' + (($unblocked | Sort-Object -Unique) -join ', ') + '.') }

# ---------- 2. Programas que están usando el micrófono ahora ----------
$busy = @()
Get-ChildItem -LiteralPath "$Consent\NonPackaged" | ForEach-Object {
    $p = Get-ItemProperty -LiteralPath $_.PSPath
    $exe = $_.PSChildName -replace '#', '\'
    if ($p.LastUsedTimeStart -gt 0 -and $p.LastUsedTimeStop -eq 0 -and $exe -notmatch $BrowserPattern) {
        $procs = @(Get-Process | Where-Object { $_.Path -eq $exe })
        if ($procs.Count -gt 0) { $busy += [pscustomobject]@{ Name = $procs[0].ProcessName; Procs = $procs } }
    }
}
Get-ChildItem -LiteralPath $Consent | Where-Object { $_.PSChildName -ne 'NonPackaged' } | ForEach-Object {
    $p = Get-ItemProperty -LiteralPath $_.PSPath
    if ($p.LastUsedTimeStart -gt 0 -and $p.LastUsedTimeStop -eq 0) {
        $name = ($_.PSChildName -split '_')[0] -replace '^[^.]+\.', ''
        $todo.Add("Cerrá «$name»: está usando el micrófono.")
    }
}
foreach ($b in $busy) {
    Write-Log "Usando el micrófono: $($b.Name)"
    $r = Show-Box ("«$($b.Name)» está usando el micrófono en este momento y puede impedir que el navegador lo use.`n`n¿Lo cierro?") 'YesNo' 'Question'
    if ($r -eq 'Yes') {
        foreach ($proc in $b.Procs) { [void]$proc.CloseMainWindow() }
        Start-Sleep -Seconds 3
        foreach ($proc in $b.Procs) { if (-not $proc.HasExited) { Stop-Process -Id $proc.Id -Force } }
        $done.Add("Cerré «$($b.Name)», que estaba usando el micrófono.")
    } else {
        $todo.Add("Cerrá «$($b.Name)» antes de practicar: está usando el micrófono.")
    }
}

# ---------- 3. Ajustes de todo el equipo (necesitan permiso de administrador) ----------
$policy = (Get-ItemProperty -Path 'HKLM:\SOFTWARE\Policies\Microsoft\Windows\AppPrivacy' -Name LetAppsAccessMicrophone).LetAppsAccessMicrophone
if ($policy -eq 2) {
    $todo.Add('Esta computadora tiene una regla de la empresa o escuela que bloquea el micrófono. Pedile al encargado de sistemas que lo habilite.')
    Write-Log 'Política LetAppsAccessMicrophone = 2 (bloqueo por organización)'
}

$adminScript = @'
$ErrorActionPreference = 'SilentlyContinue'
$out = @()
$p = 'HKLM:\SOFTWARE\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone'
if (-not (Test-Path $p)) { New-Item -Path $p -Force | Out-Null }
if ((Get-ItemProperty -Path $p -Name Value).Value -ne 'Allow') { Set-ItemProperty -Path $p -Name Value -Value 'Allow' -Type String; $out += 'equipo' }
Get-PnpDevice -Class AudioEndpoint | Where-Object { $_.Problem -eq 'CM_PROB_DISABLED' -and $_.FriendlyName -match '(?i)micr|mic |input|entrada' } | ForEach-Object {
    Enable-PnpDevice -InstanceId $_.InstanceId -Confirm:$false
    $out += 'habilitado: ' + $_.FriendlyName
}
Restart-Service -Name AudioEndpointBuilder -Force
Start-Service -Name AudioEndpointBuilder
Start-Service -Name Audiosrv
$out += 'audio reiniciado: ' + (Get-Service Audiosrv).Status
$out | Set-Content -Path (Join-Path $env:TEMP 'myteacher-microfono-admin.txt') -Encoding UTF8
'@

$r = Show-Box ("Ahora voy a revisar el micrófono para todo el equipo y reiniciar el sonido de Windows (se corta un segundo).`n`n" +
    "Windows te va a preguntar si permitís que haga cambios: tocá «Sí».`n`nSi preferís no hacerlo, tocá «No» y sigo con lo demás.") 'YesNo' 'Information'
if ($r -eq 'Yes') {
    $enc = [Convert]::ToBase64String([Text.Encoding]::Unicode.GetBytes($adminScript))
    try {
        $proc = Start-Process -FilePath 'powershell.exe' -Verb RunAs -WindowStyle Hidden -Wait -PassThru `
            -ArgumentList "-NoProfile -ExecutionPolicy Bypass -EncodedCommand $enc" -ErrorAction Stop
        $res = Get-Content -Path (Join-Path $env:TEMP 'myteacher-microfono-admin.txt') -Encoding UTF8
        Write-Log ('Administrador: ' + ($res -join ' | '))
        if ($res -contains 'equipo') { $done.Add('Activé el micrófono para todo el equipo.') }
        foreach ($line in $res) { if ($line -like 'habilitado: *') { $done.Add('Volví a habilitar «' + $line.Substring(12) + '».') } }
        $done.Add('Reinicié el sonido de Windows.')
    } catch {
        Write-Log 'Permiso de administrador no concedido.'
        $todo.Add('No hubo permiso de administrador: si sigue sin andar, volvé a abrir este asistente y tocá «Sí» cuando Windows pregunte.')
    }
}

# ---------- 4. Antivirus ----------
$av = @(Get-CimInstance -Namespace 'root/SecurityCenter2' -ClassName AntiVirusProduct |
    Where-Object { $_.displayName -notmatch '(?i)windows defender|microsoft defender' } |
    ForEach-Object { $_.displayName } | Sort-Object -Unique)
if ($av.Count -gt 0) {
    $names = $av -join ', '
    Write-Log "Antivirus: $names"
    $todo.Add("Tenés $names. Muchos antivirus bloquean el micrófono por su cuenta: abrilo, buscá «micrófono» y permití tu navegador (o desactivá la «protección de micrófono»). El asistente no puede cambiar el antivirus.")
}

# ---------- Resumen ----------
$msg = ''
if ($done.Count -gt 0) { $msg += "Listo. Esto es lo que hice:`n" + (($done | ForEach-Object { '  ✓ ' + $_ }) -join "`n") + "`n`n" }
else { $msg += "Los permisos del micrófono de Windows ya estaban bien.`n`n" }
if ($todo.Count -gt 0) { $msg += "Te falta:`n" + (($todo | ForEach-Object { '  • ' + $_ }) -join "`n") + "`n`n" }
$msg += 'Último paso: cerrá tu navegador por completo, abrilo de nuevo, entrá a MyTeacher y tocá «Decilo vos».'
Write-Log ('Hecho: ' + ($done -join ' | '))
Write-Log ('Pendiente: ' + ($todo -join ' | '))
[void](Show-Box $msg 'OK' 'Information')
