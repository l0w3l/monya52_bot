<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Гробовщик — Андрей Монинский & Нурминский</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Creepster&display=swap');

    body {
      margin: 0;
      font-family: 'Creepster', cursive;
      background: #0b0b0b;
      color: #eaeaea;
      text-align: center;
      background-image: radial-gradient(circle, #1a1a1a 0%, #000000 100%);
    }

    header {
      padding: 2rem;
      font-size: 2.5rem;
      color: #ff1c1c;
      text-shadow: 0 0 15px #8b0000;
    }

    .cover {
      width: 300px;
      height: 300px;
      margin: 2rem auto;
      background: #222;
      border: 3px solid #ff1c1c;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #666;
      font-size: 1.2rem;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .player {
      margin: 2rem auto;
    }

    .links {
      margin: 2rem auto;
      display: flex;
      justify-content: center;
      gap: 1.5rem;
      flex-wrap: wrap;
    }

    .links a {
      text-decoration: none;
      color: #eaeaea;
      background: #111;
      border: 2px solid #ff1c1c;
      padding: 0.7rem 1.2rem;
      border-radius: 8px;
      transition: all 0.3s;
      font-size: 1.2rem;
    }

    .links a:hover {
      background: #ff1c1c;
      color: #000;
      text-shadow: 0 0 5px #111;
    }

    footer {
      margin-top: 3rem;
      padding: 1rem;
      font-size: 0.9rem;
      color: #555;
    }
  </style>
</head>
<body>
  <header>
    Гробовщик<br>
    <span style="font-size:1.5rem;">Андрей Монинский × Нурминский</span>
  </header>

  <div class="cover">
	<img class="cover" src="{{ url('storage/graveyard.png') }}" />
  </div>

  <div class="player">
    <audio controls>
      <source src="{{ url('storage/graveyard.mp3') }}" type="audio/mpeg">
      Ваш браузер не поддерживает аудио.
    </audio>
  </div>

  <div class="links">
    <a href="#" target="_blank">Spotify</a>
    <a href="#" target="_blank">VK</a>
    <a href="#" target="_blank">Yandex Music</a>
    <a href="#" target="_blank">SoundCloud</a>
  </div>

  <footer>
    © 2025 Гробовщик — Все права защищены
  </footer>
</body>
</html>
