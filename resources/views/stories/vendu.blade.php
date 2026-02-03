<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendu - KEYMEX</title>
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

        /* Calque PNG (bandeau VENDU + logo) */
        .overlay-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 5;
            pointer-events: none;
        }

        /* Section infos du bien - bas gauche */
        .info-section {
            position: absolute;
            bottom: 300px;
            left: 0;
            z-index: 10;
        }

        .jours-container {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 18px 36px;
            background: rgba(255,255,255,0.95);
            border-radius: 0 8px 8px 0;
            margin-bottom: 0;
        }

        .clock-icon {
            width: 54px;
            height: 54px;
            border: 5px solid #1a1a1a;
            border-radius: 50%;
            position: relative;
        }

        .clock-icon::before,
        .clock-icon::after {
            content: '';
            position: absolute;
            background: #1a1a1a;
            left: 50%;
            top: 50%;
            transform-origin: bottom center;
        }

        .clock-icon::before {
            width: 4px;
            height: 15px;
            transform: translate(-50%, -100%) rotate(0deg);
        }

        .clock-icon::after {
            width: 4px;
            height: 11px;
            transform: translate(-50%, -100%) rotate(45deg);
        }

        .jours-text {
            font-size: 50px;
            font-weight: 800;
            color: #1a1a1a;
            letter-spacing: 2px;
        }

        .location-banner {
            background: #b71c1c;
            color: white;
            padding: 22px 45px;
            font-size: 43px;
            font-weight: 700;
            display: inline-block;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- 1. Image de fond salon -->
        @if(!empty($property['photo']))
            <img src="{{ $property['photo'] }}"
                 alt="Salon"
                 class="background-image">
        @else
            <div class="no-photo">Photo non disponible</div>
        @endif

        <!-- 2. Calque PNG (bandeau VENDU avec éclats + logo KEYMEX) -->
        @if(!empty($assets['overlay_vendu']))
            <img src="{{ $assets['overlay_vendu'] }}"
                 alt="Calque Vendu"
                 class="overlay-layer">
        @endif

        <!-- 3. Infos du bien en bas à gauche -->
        <div class="info-section">
            @if(isset($property['days']) && $property['days'] !== null)
            <div class="jours-container">
                <div class="clock-icon"></div>
                <span class="jours-text">{{ $property['days'] }} JOURS</span>
            </div>
            @endif
            <div class="location-banner">
                {{ strtoupper($property['city'] ?? '') }}{{ !empty($property['postal_code']) ? ' (' . substr($property['postal_code'], 0, 2) . ')' : '' }}
            </div>
        </div>
    </div>
</body>
</html>
