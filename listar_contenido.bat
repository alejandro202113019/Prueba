@echo off
setlocal enabledelayedexpansion

:: Define el archivo de salida
set outputFile=contenido.txt
set scriptFile=%~nx0
echo. > %outputFile%

:: Recorre cada archivo en el directorio y sus subcarpetas
for /r %%f in (*) do (
    :: Excluir el propio script y el archivo de salida
    if not "%%~nxf"=="%scriptFile%" if not "%%~nxf"=="%outputFile%" (
        echo Procesando %%f
        echo %%f >> %outputFile%
        echo. >> %outputFile%
        
        :: Añade el contenido del archivo al archivo de salida
        type "%%f" >> %outputFile%
        
        :: Añade una línea de separación entre archivos
        echo. >> %outputFile%
        echo -------------------------- >> %outputFile%
        echo. >> %outputFile%
    )
)

echo Proceso completado. El contenido ha sido guardado en %outputFile%.
pause
