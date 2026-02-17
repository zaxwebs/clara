<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos Demo — Clara</title>
    <link rel="stylesheet" href="https://unpkg.com/@knadh/oat/oat.min.css">
    <script src="https://unpkg.com/@knadh/oat/oat.min.js" defer></script>
    <style>
        body {
            max-width: 580px;
            margin: 3rem auto;
            padding: 0 1rem;
        }
        .todo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
        }
        .todo-item:last-child {
            border-bottom: none;
        }
        .todo-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            min-width: 0;
        }
        .todo-left span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .todo-left span.completed {
            text-decoration: line-through;
            opacity: 0.5;
        }
        .add-form {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .add-form input[type="text"] {
            flex: 1;
            margin: 0;
        }
        .add-form button {
            white-space: nowrap;
        }
        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            opacity: 0.6;
        }
        .counter {
            opacity: 0.5;
            font-size: 0.875rem;
        }
        header {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <nav style="display: flex; gap: 1rem; margin-bottom: 2rem;">
        <a href="/">Home</a>
        <a href="/todos">Todos Demo</a>
    </nav>

    <header>
        <h1>Todos</h1>
    </header>

    <form action="/todos" method="POST" class="add-form">
        <input type="text" name="title" placeholder="What needs to be done?" required autofocus />
        <button type="submit">Add</button>
    </form>

    <?php if (empty($todos)): ?>
        <div class="empty-state">
            <p>Nothing here yet. Add your first todo above.</p>
        </div>
    <?php else: ?>
        <article class="card" style="padding: 0;">
            <?php foreach ($todos as $todo): ?>
                <div class="todo-item">
                    <div class="todo-left">
                        <form action="/todos/toggle" method="POST">
                            <input type="hidden" name="id" value="<?= $todo['id'] ?>" />
                            <input
                                type="checkbox"
                                <?= $todo['is_completed'] ? 'checked' : '' ?>
                                onchange="this.form.submit()"
                                title="Toggle completion"
                            />
                        </form>
                        <span class="<?= $todo['is_completed'] ? 'completed' : '' ?>">
                            <?= htmlspecialchars($todo['title']) ?>
                        </span>
                    </div>
                    <form action="/todos/delete" method="POST">
                        <input type="hidden" name="id" value="<?= $todo['id'] ?>" />
                        <button type="submit" data-variant="danger" class="ghost" title="Delete todo" style="padding: 0.25rem 0.5rem;">✕</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </article>
    <?php endif; ?>
</body>
</html>
