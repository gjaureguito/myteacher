@echo off
chcp 65001 >nul
title InglesPro - Reparar el audio y el microfono
rem Se pide permiso de administrador (Windows muestra su propio aviso de control de cuentas)
net session >nul 2>&1
if not %errorlevel%==0 (
  powershell -NoProfile -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
  exit /b
)
echo ============================================================
echo   InglesPro - Reparar el audio y el microfono de Windows
echo ============================================================
echo.
echo Este archivo reinicia el servicio de audio de Windows, habilita los
echo permisos de microfono y reactiva microfonos deshabilitados.
echo No instala ni borra nada.
echo.
echo [1/4] Reiniciando el servicio de audio de Windows...
net stop Audiosrv /y >nul 2>&1
net stop AudioEndpointBuilder /y >nul 2>&1
net start AudioEndpointBuilder >nul 2>&1
net start Audiosrv >nul 2>&1
sc query Audiosrv | find "RUNNING" >nul && echo       Audio de Windows: funcionando || echo       [!] El servicio de audio no arranco. Reinicia la computadora.

echo [2/4] Habilitando los permisos de microfono...
reg add "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone" /v Value /t REG_SZ /d Allow /f >nul
reg add "HKCU\Software\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone" /v Value /t REG_SZ /d Allow /f >nul
reg add "HKCU\Software\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone\NonPackaged" /v Value /t REG_SZ /d Allow /f >nul

echo [3/4] Reactivando microfonos deshabilitados...
powershell -NoProfile -Command "$d = Get-PnpDevice -Class AudioEndpoint -PresentOnly -ErrorAction SilentlyContinue | Where-Object { $_.FriendlyName -match 'Micr' -and $_.Status -ne 'OK' }; if ($d) { $d | ForEach-Object { Enable-PnpDevice -InstanceId $_.InstanceId -Confirm:$false -ErrorAction SilentlyContinue; Write-Host ('      Reactivado: ' + $_.FriendlyName) } } else { Write-Host '      Ningun microfono estaba deshabilitado.' }"

echo [4/4] Buscando programas que suelen ocupar el microfono...
for %%p in (Zoom.exe Teams.exe ms-teams.exe Discord.exe WhatsApp.exe Skype.exe obs64.exe) do (
  tasklist /fi "imagename eq %%p" | find /i "%%p" >nul && echo       [!] %%p esta abierto: cerralo antes de probar.
)
echo.
echo Listo. Ahora cerra Chrome por completo, volve a abrirlo y toca "Probar microfono".
echo Si sigue sin funcionar, en la ventana de Sonido que se abre ahora entra al
echo microfono, Propiedades, y desmarca "Permitir que las aplicaciones tomen
echo control exclusivo de este dispositivo".
echo.
start "" ms-settings:sound
pause
