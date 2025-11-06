# 🚀 Guía de Instalación de PlanetScale CLI en Windows

## Opción 1: Instalar con Scoop (Recomendado)

### Paso 1: Abrir PowerShell como Administrador

1. Presiona `Windows + X`
2. Selecciona **"Windows PowerShell (Administrador)"** o **"Terminal (Administrador)"**
3. Acepta el aviso de UAC si aparece

### Paso 2: Instalar Scoop (si no lo tienes)

Ejecuta este comando en PowerShell:

```powershell
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
irm get.scoop.sh | iex
```

Esto instalará Scoop, un gestor de paquetes para Windows.

### Paso 3: Instalar PlanetScale CLI

```powershell
scoop install pscale
```

### Paso 4: Verificar instalación

```powershell
pscale --version
```

Si ves un número de versión, ¡está instalado correctamente!

---

## Opción 2: Instalación Manual (Sin Scoop)

### Paso 1: Descargar PlanetScale CLI

1. Ve a: https://github.com/planetscale/cli/releases
2. Busca la última versión (ej: `v0.xxx.xxx`)
3. Descarga el archivo para Windows:
   - `pscale_X.X.X_windows_amd64.zip` (para 64-bit)
   - `pscale_X.X.X_windows_386.zip` (para 32-bit)

### Paso 2: Extraer el archivo

1. Extrae el ZIP descargado
2. Encontrarás un archivo `pscale.exe`

### Paso 3: Agregar a PATH

**Opción A: Agregar manualmente al PATH**

1. Copia `pscale.exe` a una carpeta permanente, por ejemplo:
   ```
   C:\Program Files\PlanetScale\
   ```

2. Agregar al PATH:
   - Presiona `Windows + R`
   - Escribe: `sysdm.cpl` y presiona Enter
   - Ve a la pestaña **"Opciones avanzadas"**
   - Haz clic en **"Variables de entorno"**
   - En "Variables del sistema", busca `Path` y haz clic en **"Editar"**
   - Haz clic en **"Nuevo"** y agrega: `C:\Program Files\PlanetScale\`
   - Haz clic en **"Aceptar"** en todas las ventanas

**Opción B: Usar desde la carpeta actual**

Si prefieres no modificar el PATH, puedes usar `pscale.exe` directamente desde donde lo extrajiste:

```powershell
cd C:\ruta\donde\extraiste\pscale
.\pscale.exe --version
```

### Paso 4: Verificar instalación

Abre una **nueva** terminal de PowerShell y ejecuta:

```powershell
pscale --version
```

---

## Opción 3: Usar Chocolatey (Alternativa)

Si tienes Chocolatey instalado:

```powershell
choco install pscale
```

---

## ✅ Verificar que Funciona

Después de instalar, abre una **nueva terminal** de PowerShell y ejecuta:

```powershell
pscale --version
```

Deberías ver algo como:
```
pscale version 0.xxx.xxx
```

---

## 🔐 Autenticarse con PlanetScale

Una vez instalado, autentícate:

```powershell
pscale auth login
```

Esto abrirá tu navegador para que inicies sesión en PlanetScale.

---

## 📝 Próximos Pasos

Después de instalar y autenticarte:

1. **Crear base de datos:**
   ```powershell
   pscale database create diario --region us-east
   ```

2. **Crear branch de desarrollo:**
   ```powershell
   pscale branch create diario dev
   ```

3. **Conectar al branch:**
   ```powershell
   pscale connect diario dev --port 3306
   ```

4. **En otra terminal, ejecutar migraciones:**
   ```powershell
   php artisan migrate
   ```

---

## ❓ Solución de Problemas

### Error: "pscale no se reconoce como comando"

- Asegúrate de haber cerrado y reabierto la terminal después de instalar
- Verifica que el PATH esté configurado correctamente
- Prueba usar la ruta completa: `C:\Program Files\PlanetScale\pscale.exe --version`

### Error: "Execution Policy"

Si obtienes un error de política de ejecución:

```powershell
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Scoop no funciona

Si Scoop da problemas, usa la **Opción 2 (Instalación Manual)**.

---

## 📚 Recursos

- [PlanetScale CLI GitHub](https://github.com/planetscale/cli)
- [Documentación de PlanetScale](https://docs.planetscale.com)
- [Scoop.sh](https://scoop.sh)

