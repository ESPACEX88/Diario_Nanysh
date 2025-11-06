# 🔄 Configuración Keep-Alive para Render (Gratis)

Render suspende los servicios gratuitos después de 15 minutos de inactividad. Para mantener tu aplicación siempre activa **sin pagar**, puedes usar servicios gratuitos que hacen peticiones periódicas.

## ✅ Solución: Usar Servicios Gratuitos de Ping

### Opción 1: UptimeRobot (Recomendado) ⭐

1. **Regístrate gratis**: https://uptimerobot.com/
2. **Crea un nuevo monitor**:
   - Tipo: HTTP(s)
   - Nombre: Diario de Nanysh
   - URL: `https://tu-app.onrender.com/keep-alive`
   - Intervalo: 5 minutos (gratis)
   - Click en "Create Monitor"

**Ventajas:**
- ✅ Completamente gratis
- ✅ Hasta 50 monitores
- ✅ Intervalo mínimo de 5 minutos (suficiente)
- ✅ Notificaciones si el servicio cae

### Opción 2: cron-job.org

1. **Regístrate gratis**: https://cron-job.org/
2. **Crea un nuevo cron job**:
   - URL: `https://tu-app.onrender.com/keep-alive`
   - Intervalo: Cada 10 minutos
   - Método: GET
   - Click en "Create Cronjob"

**Ventajas:**
- ✅ Gratis
- ✅ Muy confiable
- ✅ Puedes configurar intervalos personalizados

### Opción 3: EasyCron

1. **Regístrate gratis**: https://www.easycron.com/
2. **Crea un nuevo cron job**:
   - URL: `https://tu-app.onrender.com/keep-alive`
   - Intervalo: Cada 10 minutos
   - Método: GET

### Opción 4: Pingdom (Solo para prueba)

- Tiene plan gratuito limitado
- Útil para pruebas

## 📝 Endpoint Creado

He creado un endpoint especial en tu aplicación:

```
GET https://tu-app.onrender.com/keep-alive
```

Este endpoint:
- ✅ No requiere autenticación
- ✅ Responde rápidamente
- ✅ Devuelve un JSON simple
- ✅ Mantiene el servicio "despierto"

## 🚀 Pasos para Configurar

1. **Despliega tu aplicación en Render** (si no lo has hecho)
2. **Copia la URL de tu aplicación**: `https://tu-app.onrender.com`
3. **Elige uno de los servicios arriba** (recomiendo UptimeRobot)
4. **Configura el monitor/cron job** con la URL: `https://tu-app.onrender.com/keep-alive`
5. **Establece el intervalo**: Cada 5-10 minutos
6. **¡Listo!** Tu aplicación se mantendrá activa

## ⚠️ Notas Importantes

- **Intervalo recomendado**: 5-10 minutos (menos de 15 minutos de inactividad)
- **El endpoint es público**: No contiene información sensible
- **No afecta el rendimiento**: Es una petición muy ligera
- **Gratis**: Todos los servicios mencionados tienen planes gratuitos

## 🔍 Verificar que Funciona

Puedes probar el endpoint manualmente:

```bash
curl https://tu-app.onrender.com/keep-alive
```

Deberías recibir:
```json
{
  "status": "ok",
  "message": "Servicio activo",
  "timestamp": "2025-11-06 22:00:00"
}
```

## 💡 Alternativa: Usar el Dashboard

Si no quieres usar servicios externos, también puedes:
- Abrir el dashboard periódicamente desde tu navegador
- Usar una extensión de navegador que refresque la página automáticamente
- Pero esto requiere que tengas el navegador abierto

**La mejor opción es UptimeRobot** - es gratis, confiable y fácil de configurar.

