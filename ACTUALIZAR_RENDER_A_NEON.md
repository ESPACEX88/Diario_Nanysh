# 🚀 Guía Rápida: Actualizar Render para Usar Neon

## ⚡ Pasos Rápidos (5 minutos)

### 1. Obtén tu Cadena de Conexión de Neon

Ve a https://console.neon.tech → Tu proyecto → **"Connection Details"** → Copia la cadena completa

### 2. Actualiza en Render

1. https://dashboard.render.com
2. Servicio web "diario-nahysh"
3. **Environment** → Busca `DATABASE_URL`
4. **Pega tu cadena de Neon**
5. **Save Changes**

### 3. Reinicia

Click en **"Manual Deploy"** → **"Deploy latest commit"**

### 4. Listo

Tu app funcionará con Neon. Los datos antiguos se perdieron, pero la app funciona perfectamente.

---

## 📝 Formato de la Cadena de Conexión

Debería verse así:
```
postgresql://usuario:password@ep-xxxxx-xxxxx.us-east-2.aws.neon.tech/neondb?sslmode=require
```

**IMPORTANTE**: Debe incluir `?sslmode=require` al final.

---

## ✅ Verificación

Después de desplegar, abre tu app y verifica que:
- ✅ Puedes iniciar sesión o crear cuenta
- ✅ No hay errores de conexión
- ✅ La app funciona normalmente

---

## 🔧 Si Necesitas Ayuda

Si tienes problemas, verifica:
1. Que la cadena de conexión esté completa
2. Que incluya `?sslmode=require`
3. Que hayas guardado los cambios en Render
4. Que el servicio se haya desplegado correctamente

