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
<!--            --><?php //if ($posts): ?>
<!--                --><?php //foreach ($posts as $p): ?>
<!--                    <div class="border-b border-gray-200 pb-4 mb-4">-->
<!--                        <p class="font-semibold text-gray-900">--><?php //= htmlspecialchars($p['name']) ?><!--</p>-->
<!--                        <p class="text-gray-700 mt-2">--><?php //= nl2br(htmlspecialchars($p['content'])) ?><!--</p>-->
<!--                        --><?php //if ($p['image']): ?>
<!--                            <img src="--><?php //= htmlspecialchars($p['image']) ?><!--" alt="Post Image" class="mt-3 rounded-lg max-h-64 object-cover w-full">-->
<!--                        --><?php //endif; ?>
<!--                        <p class="text-xs text-gray-500 mt-2">--><?php //= htmlspecialchars($p['created_at']) ?><!--</p>-->
<!--                    </div>-->
<!--                --><?php //endforeach; ?>
<!--            --><?php //else: ?>
<!--                <p class="text-gray-500">No posts yet. Be the first to share something!</p>-->
<!--            --><?php //endif; ?>
            <?php if ($posts): ?>
                <?php foreach ($posts as $p): ?>
                    <?php
                    $isOwner = !empty($user) && $p['user_id'] == $user['id'];
                    $created = new DateTime($p['created_at']);
                    $now = new DateTime();
                    $secondsSince = $now->getTimestamp() - $created->getTimestamp();
                    $canEdit = $isOwner && $secondsSince <= 24 * 60 * 60;
                    $isEdited = !empty($p['edited_at']);
                    ?>
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-900">
                                    <?= htmlspecialchars($p['name']) ?>
                                    <?php if ($isEdited): ?>
                                        <span class="text-xs text-gray-500">(edited)</span>
                                    <?php endif; ?>
                                </p>
                                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($p['created_at']) ?></p>
                            </div>

                            <?php if ($isOwner): ?>
                                <div class="flex gap-2">

                                    <?php if ($canEdit): ?>
                                        <a href="/post/edit?id=<?= htmlspecialchars($p['id']) ?>"
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-md">
                                            Edit
                                        </a>
                                    <?php endif; ?>

                                    <form method="POST" action="/post/delete" onsubmit="return confirm('Delete this post?');">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-sm rounded-md">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                        </div>

                        <p class="text-gray-700 mt-2"><?= nl2br(htmlspecialchars($p['content'])) ?></p>

                        <?php if ($p['image']): ?>
                            <img src="<?= htmlspecialchars($p['image']) ?>" alt=""
                                 class="mt-3 rounded-lg max-h-64 object-cover w-full">
                        <?php endif; ?>
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
