<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($message) ?> — Clara</title>
    <link rel="stylesheet" href="https://unpkg.com/@knadh/oat/oat.min.css">
    <script src="https://unpkg.com/@knadh/oat/oat.min.js" defer></script>
    <style>
        body {
            max-width: 580px;
            margin: 3rem auto;
            padding: 0 1rem;
        }
    </style>
</head>
<body>
    <nav style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <a href="/">Home</a>
        <a href="/todos">Todos Demo</a>
    </nav>

    <header>
        <h1><?= htmlspecialchars($message) ?></h1>
        <p>Welcome to Clara.</p>
    </header>
</body>
</html>
