<?php
$title = 'Search | AuthBoard';
ob_start();
?>
    <div class="max-w-3xl mx-auto">


        <?php if ($q === ''): ?>
            <p class="text-gray-500">Enter a search term to begin.</p>
        <?php else: ?>
            <h3 class="text-lg font-medium mb-3 text-gray-800">
                Results for <span class="font-semibold">"<?= htmlspecialchars($q) ?>"</span> (<?= htmlspecialchars($type) ?>)
            </h3>

            <?php if (empty($results)): ?>
                <p class="text-gray-500">No results found.</p>
            <?php else: ?>

                <?php if ($type === 'users'): ?>
                    <div class="space-y-4">
                        <?php foreach ($results as $u): ?>
                            <div class="p-4 bg-white rounded-md shadow-sm border">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold"><?= htmlspecialchars($u['name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($u['email']) ?></p>
                                    </div>
                                    <div class="text-xs text-gray-400"><?= htmlspecialchars($u['created_at'] ?? '') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php else: /* posts */ ?>
                    <div class="space-y-6">
                        <?php foreach ($results as $p): ?>
                            <div class="p-4 bg-white rounded-md shadow-sm border">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold"><?= htmlspecialchars($p['name']) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($p['created_at']) ?></p>
                                    </div>
                                    <?php if (!empty($p['user_id']) && $p['user_id'] === ($user['id'] ?? null)): ?>
                                        <div class="text-xs text-gray-400">Your post</div>
                                    <?php endif; ?>
                                </div>

                                <p class="mt-3 text-gray-700"><?= nl2br(htmlspecialchars($p['content'])) ?></p>

                                <?php if (!empty($p['image'])): ?>
                                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="Post image" class="mt-3 rounded-lg max-h-64 object-cover w-full">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
