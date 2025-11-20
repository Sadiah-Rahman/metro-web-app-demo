<?php
$title = 'Dashboard | AuthBoard';

use App\Models\Post;

// Get all posts
$posts = Post::all();

// Helper: Initials from name
function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    $letters = array_map(fn($p) => mb_substr($p, 0, 1), array_filter($parts));
    $initials = strtoupper(implode('', $letters));
    return mb_substr($initials, 0, 2);
}

// Helper: format timestamp
function fmtDate(string $ts): string {
    try {
        $d = new DateTime($ts);
        return $d->format("Y-m-d H:i:s");
    } catch (Exception $e) {
        return htmlspecialchars($ts);
    }
}

ob_start();
?>

<div class="space-y-10">

    <!-- Post form -->
    <div class="bg-white shadow-md rounded-lg p-6 border border-gray-200">
        <h2 class="text-xl font-semibold mb-4 text-blue-700">
            Welcome, <?= htmlspecialchars($user['name']) ?>
        </h2>

        <form method="POST" action="/post" enctype="multipart/form-data" class="space-y-4">
            <textarea name="content" placeholder="What's on your mind?"
                      class="w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500"></textarea>

            <input type="file" name="image" class="block w-full text-sm text-gray-700">

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold">
                Post
            </button>
        </form>
    </div>

    <!-- Recent posts -->
    <div class="space-y-6">

        <?php if ($posts): ?>
            <?php foreach ($posts as $p): ?>

                <?php
                $isOwner = !empty($user) && $p['user_id'] == $user['id'];

                // 24h edit window
                $createdAt = new DateTime($p['created_at']);
                $now = new DateTime();
                $canEdit = ($now->getTimestamp() - $createdAt->getTimestamp()) <= 24 * 60 * 60;

                $avatarText = initials($p['name']);
                ?>

                <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                    <div class="p-5 flex gap-4">

                        <!-- Avatar -->
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <a href="/user?id=<?= htmlspecialchars($p['user_id']) ?>">
                                <?php if (!empty($p['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($p['avatar']) ?>" alt="avatar" class="w-12 h-12 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-lg">
                                        <?= htmlspecialchars($avatarText) ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>

                        <!-- Name should also link -->
<!--                        <p class="font-semibold text-gray-900">-->
<!--                            <a href="/user?id=--><?php //= htmlspecialchars($p['user_id']) ?><!--" class="hover:underline">-->
<!--                                --><?php //= htmlspecialchars($p['name']) ?>
<!--                            </a>-->
<!--                        </p>-->


                        <!-- Main Post Content -->
                        <div class="flex-1">

                            <!-- User Header -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars($p['email']) ?> •
                                        <span class="text-xs text-gray-400">
                                            <?= htmlspecialchars(fmtDate($p['created_at'])) ?>
                                        </span>
                                    </p>
                                </div>

                                <!-- Actions -->
                                <?php if ($isOwner): ?>
                                    <div class="flex items-center gap-2">

                                        <?php if ($canEdit): ?>
                                            <a href="/post/edit?id=<?= htmlspecialchars($p['id']) ?>"
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-md">
                                                Edit
                                            </a>
                                        <?php endif; ?>

                                        <form method="POST" action="/post/delete"
                                              onsubmit="return confirm('Delete this post?');">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-md">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="mt-4 text-gray-800 leading-relaxed">
                                <?= nl2br(htmlspecialchars($p['content'])) ?>
                            </div>

                            <!-- Image -->
                            <?php if (!empty($p['image'])): ?>
                                <div class="mt-4">
                                    <img src="<?= htmlspecialchars($p['image']) ?>"
                                         class="w-full rounded-md object-cover max-h-96">
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>
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
?>
