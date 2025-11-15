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
                <form method="GET" action="/search" class="hidden sm:block">
                    <input
                            type="text"
                            name="q"
                            placeholder="Search..."
                            class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-2 focus:ring-blue-500"
                    >
                    <input type="hidden" name="type" value="posts">
                </form>

                <nav class="space-x-4">
                    <a href="/dashboard" class="text-blue-600 hover:underline">Dashboard</a>
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
