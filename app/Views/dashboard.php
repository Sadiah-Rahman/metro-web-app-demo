<?php
$title = 'Dashboard | AuthBoard';
ob_start();
use App\Models\Post;
$posts = Post::all();
?>
    <div class="space-y-10">

        <!-- Post form -->
        <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
            <h2 class="text-xl font-semibold mb-4 text-blue-700">Welcome, <?= htmlspecialchars($user['name']) ?></h2>
            <form method="POST" action="/post" enctype="multipart/form-data" class="space-y-4">
                <textarea name="content" placeholder="What's on your mind?" class="w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500"></textarea>
                <input type="file" name="image" class="block w-full text-sm text-gray-700">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold">Post</button>
            </form>
        </div>

        <!-- Recent posts -->
        <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Recent Posts</h3>
            <?php if ($posts): ?>
                <?php foreach ($posts as $p): ?>
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <p class="font-semibold text-gray-900"><?= htmlspecialchars($p['name']) ?></p>
                        <p class="text-gray-700 mt-2"><?= nl2br(htmlspecialchars($p['content'])) ?></p>
                        <?php if ($p['image']): ?>
                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="Post Image" class="mt-3 rounded-lg max-h-64 object-cover w-full">
                        <?php endif; ?>
                        <p class="text-xs text-gray-500 mt-2"><?= htmlspecialchars($p['created_at']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">No posts yet. Be the first to share something!</p>
            <?php endif; ?>
        </div>

    </div>
<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
