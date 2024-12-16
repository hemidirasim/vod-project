<!DOCTYPE html>
<html>
<head>
    <title>VOD Player</title>
    <link href="https://vjs.zencdn.net/7.20.3/video-js.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/7.20.3/video.min.js"></script>
    <style>
        :root {
            --primary-color: #E50914;
            --hover-color: #F40612;
            --bg-dark: #141414;
            --bg-darker: #0B0B0B;
            --text-light: #E5E5E5;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--bg-darker);
            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--text-light);
        }

        .main-container {
            max-width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .player-header {
            background: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, transparent 100%);
            padding: 20px 40px;
            position: fixed;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            box-sizing: border-box;
        }

        .channel-logo {
            height: 40px;
            filter: brightness(0) invert(1);
        }

        .quality-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .quality-btn {
            background: rgba(255,255,255,0.1);
            border: none;
            color: var(--text-light);
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .quality-btn.active {
            background: var(--primary-color);
        }

        .quality-btn:hover:not(.active) {
            background: rgba(255,255,255,0.2);
        }

        .player-wrapper {
            flex: 1;
            position: relative;
            background: black;
        }

        .video-js {
            width: 100%;
            height: 100%;
        }

        /* Custom Video.js theme */
        .video-js .vjs-control-bar {
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            height: 5em;
            padding: 0 2em;
        }

        .video-js .vjs-progress-control {
            position: absolute;
            width: 100%;
            top: -1em;
            height: 0.6em;
        }

        .video-js .vjs-progress-control .vjs-progress-holder {
            margin: 0;
        }

        .video-js .vjs-play-progress {
            background: var(--primary-color);
        }

        .video-js .vjs-load-progress {
            background: rgba(255,255,255,0.3);
        }

        .info-overlay {
            position: absolute;
            left: 40px;
            bottom: 100px;
            color: var(--text-light);
            z-index: 900;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .player-wrapper:hover .info-overlay {
            opacity: 1;
        }

        .info-overlay h1 {
            font-size: 2.5em;
            margin: 0 0 10px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .info-overlay p {
            font-size: 1.2em;
            margin: 5px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            background: var(--primary-color);
            padding: 5px 12px;
            border-radius: 4px;
            gap: 6px;
            font-weight: 500;
        }

        .live-indicator {
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Custom buffer animation */
        .custom-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 3px solid transparent;
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="main-container">
        <!-- Header -->
        <div class="player-header">
            <img src="/logo.png" alt="Channel Logo" class="channel-logo">
            <div class="quality-controls">
                <button class="quality-btn active">720p HD</button>
                <button class="quality-btn">480p</button>
                <button class="quality-btn">360p</button>
            </div>
        </div>

        <!-- Player -->
        <div class="player-wrapper">
            <video-js id="vod-player" class="video-js vjs-big-play-centered">
                <source src="?route=play&date=<?= date('Y-m-d') ?>" type="application/x-mpegURL">
            </video-js>

            <!-- Info Overlay -->
            <div class="info-overlay">
                <div class="status-badge">
                    <div class="live-indicator"></div>
                    CANLI
                </div>
                <h1>Aktual</h1>
                <p>HD Keyfiyyət • Canlı Yayım</p>
            </div>

            <!-- Custom Buffer Animation -->
            <div class="custom-spinner" style="display: none;"></div>
        </div>
    </div>

    <script>
        var player = videojs('vod-player', {
            controls: true,
            autoplay: true,
            preload: 'auto',
            fluid: true,
            controlBar: {
                children: [
                    'playToggle',
                    'volumePanel',
                    'progressControl',
                    'remainingTimeDisplay',
                    'fullscreenToggle'
                ]
            }
        });

        // Buffer handling
        player.on('waiting', function() {
            document.querySelector('.custom-spinner').style.display = 'block';
        });

        player.on('playing', function() {
            document.querySelector('.custom-spinner').style.display = 'none';
        });

        // Quality selection
        document.querySelectorAll('.quality-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.quality-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Here you would implement quality switching logic
                const quality = this.textContent;
                console.log('Switching to:', quality);
            });
        });
    </script>
</body>
</html>