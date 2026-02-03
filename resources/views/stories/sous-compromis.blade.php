<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sous Compromis - KEYMEX</title>
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

        /* Image de fond (façade maison) */
        .background-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: grayscale(100%) contrast(1.1);
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

        /* Calque PNG (bandeaux rouges + logo) */
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

        /* Bloc blanc en haut avec type de bien et ville */
        .top-banner {
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 30px 60px;
            text-align: center;
            z-index: 10;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .top-title {
            color: #b71c1c;
            font-size: 72px;
            font-weight: 800;
            letter-spacing: 2px;
            line-height: 1;
            margin-bottom: 10px;
        }

        .top-location {
            color: #b71c1c;
            font-size: 42px;
            font-weight: 600;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- 1. Image de fond façade maison -->
        @if(!empty($property['photo']))
            <img src="{{ $property['photo'] }}"
                 alt="Photo du bien"
                 class="background-image">
        @else
            <div class="no-photo">Photo non disponible</div>
        @endif

        <!-- 2. Calque PNG (bandeaux rouges + logo KEYMEX en bas) -->
        @if(!empty($assets['overlay_compromis']))
            <img src="{{ $assets['overlay_compromis'] }}"
                 alt="Calque Sous Compromis"
                 class="overlay-layer">
        @endif

        <!-- 3. Bloc blanc en haut -->
        <div class="top-banner">
            <div class="top-title">{{ strtoupper($property['type'] ?? 'BIEN IMMOBILIER') }}</div>
            <div class="top-location">{{ strtoupper($property['city'] ?? '') }}{{ $property['postal_code'] ? ' (' . substr($property['postal_code'], 0, 2) . ')' : '' }}</div>
        </div>
    </div>
</body>
</html>
