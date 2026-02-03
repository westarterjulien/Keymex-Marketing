<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Mandat - KEYMEX</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 1080px;
            height: 1920px;
            font-family: 'Montserrat', Arial, sans-serif;
            background: #fff;
            overflow: hidden;
        }

        .card {
            width: 1080px;
            height: 1920px;
            position: relative;
            background: #fff;
            overflow: hidden;
            border: 48px solid #ffffff;
            border-radius: 24px;
        }

        /* Image de fond (salon) */
        .background-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 12px;
        }

        .no-photo {
            width: 100%;
            height: 100%;
            background: #e5e5e5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 36px;
            border-radius: 12px;
        }

        /* Filtre blanc entre photo et calque */
        .white-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.45);
            z-index: 2;
            pointer-events: none;
            border-radius: 12px;
        }

        /* Calque PNG avec tout sauf ville et logo */
        .overlay-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 3;
            pointer-events: none;
        }

        /* Bandeau blanc avec la ville */
        .location-banner {
            position: absolute;
            top: 520px;
            right: 0;
            background: white;
            padding: 20px 50px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .burst-icon {
            width: 40px;
            height: 40px;
            fill: #b71c1c;
        }

        .location-text {
            color: #b71c1c;
            font-size: 48px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* Logo KEYMEX en bas */
        .logo-section {
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 10;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-image {
            height: 140px;
            width: auto;
            max-width: 600px;
            object-fit: contain;
        }

        .logo-fallback {
            font-size: 64px;
            font-weight: 800;
            color: #b71c1c;
            letter-spacing: 6px;
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- 1. Image de fond du bien -->
        @if(!empty($property['photo']))
            <img src="{{ $property['photo'] }}"
                 alt="Photo du bien"
                 class="background-image">
        @else
            <div class="no-photo">Photo non disponible</div>
        @endif

        <!-- 2. Filtre blanc par-dessus la photo -->
        <div class="white-overlay"></div>

        <!-- 3. Calque PNG (par-dessus le filtre blanc) -->
        @if(!empty($assets['overlay']))
            <img src="{{ $assets['overlay'] }}"
                 alt="Calque"
                 class="overlay-layer">
        @endif

        <!-- 4. Bandeau blanc avec la ville -->
        <div class="location-banner">
            <svg class="burst-icon" viewBox="0 0 24 24" fill="#b71c1c">
                <path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z"/>
            </svg>
            <span class="location-text">{{ strtoupper($property['city'] ?? '') }}{{ $property['postal_code'] ? ' (' . substr($property['postal_code'], 0, 2) . ')' : '' }}</span>
        </div>

        <!-- 5. Logo KEYMEX -->
        <div class="logo-section">
            @if(!empty($assets['logo']))
                <img src="{{ $assets['logo'] }}"
                     alt="KEYMEX"
                     class="logo-image">
            @else
                <div class="logo-fallback">KEYMEX</div>
            @endif
        </div>
    </div>
</body>
</html>
