<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'AuthBoard' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<script>
    function confirmDelete(form){
        return confirm('Are you sure you want to delete this post? This action cannot be undone.');
    }
</script>

<body class="bg-gray-100 text-gray-900 min-h-screen">
<div class="max-w-3xl mx-auto my-10 bg-white rounded-xl shadow-lg p-6">


    <header class="flex justify-between items-center mb-8 border-b pb-4">
        <h1 class="text-2xl font-bold text-blue-700">AuthBoard</h1>

        <?php if (isset($user) && !empty($user)): ?>
            <div class="flex items-center space-x-4">

                <!-- Avatar / Profile link -->
                <?php if (!empty($user['avatar'])): ?>
                    <a href="/user?id=<?= htmlspecialchars($user['id']) ?>">
                        <img src="<?= htmlspecialchars($user['avatar']) ?>"
                             class="w-8 h-8 rounded-full object-cover border">
                    </a>
                <?php else: ?>
                    <a href="/user?id=<?= htmlspecialchars($user['id']) ?>">
                        <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-sm">
                            <?= htmlspecialchars(substr($user['name'], 0, 1)) ?>
                        </div>
                    </a>
                <?php endif; ?>

                <!-- Search form (visible for logged-in users) -->
                <form method="GET" action="/search" class="flex items-center gap-2">
                    <input
                            type="text"
                            name="q"
                            placeholder="Search posts or users..."
                            class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500"
                    />
                    <select name="type" class="hidden">
                        <option value="posts" selected>Posts</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        🔍
                    </button>
                </form>

                <!-- Nav Links -->
                <nav class="space-x-4">
                    <a href="/dashboard" class="text-blue-600 hover:underline">Dashboard</a>
                    <a href="/user?id=<?= htmlspecialchars($user['id']) ?>" class="text-blue-600 hover:underline">Profile</a>
                    <a href="/logout" class="text-red-600 hover:underline">Logout</a>
                </nav>
            </div>
        <?php endif; ?>
    </header>



    <main>

        <?= $content ?>
    </main>

    <footer class="mt-10 text-center text-sm text-gray-500 border-t pt-4">
        AuthBoard &copy; <?= date('Y') ?>
    </footer>
</div>
</body>
</html>
