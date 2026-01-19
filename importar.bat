@echo off
echo === Importando Backup a Supabase ===
echo.

set PGPASSWORD=Sksakmak47.#

"C:\Program Files\PostgreSQL\17\bin\pg_restore.exe" -h db.shaqukxfvhtwngqizzkx.supabase.co -p 5432 -U postgres -d postgres --no-owner --no-acl --clean --if-exists -v "C:\Users\posad\Downloads\2026-01-19T16_30Z.dir\2026-01-19T16_30Z\diario_fhd4"

echo.
echo === Importacion completada ===
echo Verifica en Supabase Table Editor si los datos estan presentes
echo.
pause
