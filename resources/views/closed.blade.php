<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-notify-fix" content="visit-ip-v5">
    <title>Diario de Nahysh — Cerrado</title>
    <!-- notify: {{ $notifyStatus ?? 'n/a' }} -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,700&family=Nunito:wght@500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #3a2148;
            --soft: #fff7fb;
            --rose: #ff6b9d;
            --lilac: #c084fc;
            --sky: #7dd3fc;
            --gold: #fbbf24;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Nunito', system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 18%, rgba(255, 107, 157, 0.35), transparent 42%),
                radial-gradient(circle at 88% 12%, rgba(125, 211, 252, 0.4), transparent 38%),
                radial-gradient(circle at 70% 85%, rgba(192, 132, 252, 0.35), transparent 45%),
                linear-gradient(160deg, #fff1f7 0%, #eef6ff 45%, #f5e9ff 100%);
            display: grid;
            place-items: center;
            padding: 2rem 1.25rem;
            overflow-x: hidden;
        }

        .scene {
            width: min(640px, 100%);
            text-align: center;
            position: relative;
        }

        .orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(2px);
            animation: float 7s ease-in-out infinite;
            pointer-events: none;
        }

        .orb.a { width: 90px; height: 90px; background: rgba(255,107,157,.35); top: -30px; left: -20px; }
        .orb.b { width: 70px; height: 70px; background: rgba(125,211,252,.4); top: 40px; right: -10px; animation-delay: -2s; }
        .orb.c { width: 55px; height: 55px; background: rgba(251,191,36,.35); bottom: 20px; left: 30px; animation-delay: -4s; }

        .card {
            position: relative;
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
            border: 2px solid rgba(255, 255, 255, 0.85);
            border-radius: 2rem;
            padding: 2.75rem 1.75rem 2.25rem;
            box-shadow:
                0 25px 60px rgba(90, 40, 100, 0.12),
                inset 0 1px 0 rgba(255,255,255,0.9);
        }

        .faces {
            display: flex;
            justify-content: center;
            gap: 0.6rem;
            font-size: clamp(2.4rem, 8vw, 3.4rem);
            margin-bottom: 1rem;
            animation: sniff 3.5s ease-in-out infinite;
            filter: drop-shadow(0 8px 16px rgba(255, 107, 157, 0.25));
        }

        h1 {
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 700;
            font-size: clamp(1.9rem, 6vw, 2.7rem);
            line-height: 1.15;
            margin-bottom: 0.85rem;
            background: linear-gradient(120deg, #db2777, #7c3aed 55%, #0284c7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .subtitle {
            font-size: 1.15rem;
            font-weight: 800;
            color: #be185d;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.05rem;
            line-height: 1.65;
            color: #5b3b66;
            max-width: 34ch;
            margin: 0 auto 1.4rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.65rem 1.1rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #ffe4ef, #e9d5ff);
            border: 1px solid rgba(219, 39, 119, 0.2);
            color: #9d174d;
            font-weight: 800;
            font-size: 0.95rem;
            box-shadow: 0 10px 24px rgba(190, 24, 93, 0.12);
        }

        .tears {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            border-radius: 2rem;
        }

        .tear {
            position: absolute;
            top: -20px;
            width: 10px;
            height: 14px;
            background: linear-gradient(#7dd3fc, #38bdf8);
            border-radius: 50% 50% 50% 50% / 40% 40% 60% 60%;
            opacity: 0.55;
            animation: fall 4.8s linear infinite;
        }

        .tear:nth-child(1) { left: 18%; animation-delay: 0s; }
        .tear:nth-child(2) { left: 42%; animation-delay: 1.2s; }
        .tear:nth-child(3) { left: 67%; animation-delay: 0.6s; }
        .tear:nth-child(4) { left: 82%; animation-delay: 2.1s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-14px); }
        }

        @keyframes sniff {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(6px) rotate(2deg); }
        }

        @keyframes fall {
            0% { transform: translateY(0) scale(0.8); opacity: 0; }
            15% { opacity: 0.6; }
            100% { transform: translateY(420px) scale(1); opacity: 0; }
        }
    </style>
</head>
<body>
    <main class="scene">
        <div class="orb a" aria-hidden="true"></div>
        <div class="orb b" aria-hidden="true"></div>
        <div class="orb c" aria-hidden="true"></div>

        <section class="card" role="status" aria-live="polite">
            <div class="tears" aria-hidden="true">
                <span class="tear"></span>
                <span class="tear"></span>
                <span class="tear"></span>
                <span class="tear"></span>
            </div>

            <div class="faces" aria-hidden="true">😢 🥺 💔 😭</div>
            <h1>Este diario ya cerró</h1>
            <p class="subtitle">Adiós para siempre…</p>
            <p>
                Este sitio ya no se usa y se quedó en silencio.
                Gracias por los momentos que guardó. Ahora solo queda esta carita triste.
            </p>
            <div class="badge">
                <span aria-hidden="true">☹️</span>
                Servicio cerrado permanentemente
            </div>
        </section>
    </main>
    <script>
        (function () {
            // Aviso desde el navegador del visitante (evita el rate-limit 429 de la IP de Render).
            var topic = @json($ntfyTopic ?? 'diario-nahysh-visitas-5660d0');
            if (!topic) {
                return;
            }
            var key = 'nahysh-visit-ntfy:' + topic;
            try {
                if (sessionStorage.getItem(key) === '1') {
                    return;
                }
            } catch (e) {}

            var body = [
                'Vieron el mensaje de despedida 😢',
                'Desde: navegador del visitante',
                'Hora local: ' + new Date().toLocaleString(),
                'Navegador: ' + String(navigator.userAgent || '').slice(0, 160)
            ].join('\n');

            fetch('https://ntfy.sh/' + encodeURIComponent(topic), {
                method: 'POST',
                headers: {
                    'Title': 'Alguien visitó el Diario de Nahysh',
                    'Priority': 'high',
                    'Tags': 'sobbing_face,broken_heart',
                    'Content-Type': 'text/plain'
                },
                body: body,
                mode: 'cors',
                keepalive: true
            }).then(function (res) {
                if (res && res.ok) {
                    try { sessionStorage.setItem(key, '1'); } catch (e) {}
                }
            }).catch(function () {});
        })();
    </script>
</body>
</html>
