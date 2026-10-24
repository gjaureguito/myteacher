@echo off
chcp 65001 >nul
title InglesPro - Activar el microfono
echo ============================================================
echo   InglesPro - Activar el microfono para Chrome y Edge
echo ============================================================
echo.
echo Este archivo solo cambia los permisos de privacidad del microfono
echo de Windows. No instala nada ni toca otros archivos.
echo.

reg query "HKLM\SOFTWARE\Policies\Microsoft\Windows\AppPrivacy" /v LetAppsAccessMicrophone >nul 2>&1
if %errorlevel%==0 (
  echo [!] El microfono esta controlado por una politica de la empresa o institucion.
  echo     Pedile al administrador de sistemas que lo habilite.
  echo.
)

echo [1/3] Permitiendo el acceso al microfono para tu usuario...
reg add "HKCU\Software\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone" /v Value /t REG_SZ /d Allow /f >nul
echo [2/3] Permitiendo el microfono a las aplicaciones de escritorio (Chrome, Edge)...
reg add "HKCU\Software\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone\NonPackaged" /v Value /t REG_SZ /d Allow /f >nul

net session >nul 2>&1
if %errorlevel%==0 (
  echo [3/3] Permitiendo el microfono en todo el equipo y reiniciando el audio de Windows...
  reg add "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\microphone" /v Value /t REG_SZ /d Allow /f >nul
  net stop Audiosrv /y >nul 2>&1
  net start Audiosrv >nul 2>&1
) else (
  echo [3/3] Omitido: para el ajuste de todo el equipo y reiniciar el audio,
  echo       cerra esta ventana y ejecuta el archivo con clic derecho -^> "Ejecutar como administrador".
)

echo.
echo Listo. Ahora:
echo   1. Cerra Chrome por completo (todas las ventanas) y volve a abrirlo.
echo   2. Entra a InglesPro y toca "Probar microfono".
echo.
start "" ms-settings:privacy-microphone
pause
