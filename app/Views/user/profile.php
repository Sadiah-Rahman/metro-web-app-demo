<?php
$title = htmlspecialchars($profile['name'] . ' | Profile');
ob_start();

function initials(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    $letters = array_map(fn($p) => mb_substr($p, 0, 1), array_filter($parts));
    return strtoupper(mb_substr(implode('', $letters), 0, 2));
}

function fmtDate(string $ts): string {
    try { $d = new DateTime($ts); return $d->format('F j, Y'); }
    catch (Exception $e) { return htmlspecialchars($ts); }
}
?>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 border mb-6 flex items-center gap-4">
            <div>
                <?php if (!empty($profile['avatar'])): ?>
                    <img src="<?= htmlspecialchars($profile['avatar']) ?>" alt="avatar" class="w-20 h-20 rounded-full object-cover">
                <?php else: ?>
                    <div class="w-20 h-20 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold text-xl">
                        <?= htmlspecialchars(initials($profile['name'])) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-lg font-semibold"><?= htmlspecialchars($profile['name']) ?></h1>
                        <p class="text-sm text-gray-500"><?= htmlspecialchars($profile['email']) ?></p>
                        <p class="text-xs text-gray-400 mt-1">Joined <?= fmtDate($profile['created_at']) ?></p>
                        <?php if (!empty($profile['bio'])): ?>
                            <p class="mt-3 text-gray-700"><?= nl2br(htmlspecialchars($profile['bio'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="text-right">
                        <div class="text-sm text-gray-700 font-medium"><?= (int)$followers ?> followers</div>

                        <?php if (!empty($user) && $user['id'] == $profile['id']): ?>
                            <a href="/user/edit" class="mt-3 inline-block px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-md text-sm">Edit Profile</a>
                        <?php elseif (!empty($user)): ?>
                            <?php if ($isFollowing): ?>
                                <form method="POST" action="/user/unfollow" class="mt-3">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($profile['id']) ?>">
                                    <button type="submit" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-md text-sm">Unfollow</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="/user/follow" class="mt-3">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($profile['id']) ?>">
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">Follow</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/login" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Log in to follow</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posts by user -->
        <div class="space-y-4">
            <?php if (empty($posts)): ?>
                <div class="bg-white p-4 rounded border text-gray-500">This user hasn't posted yet.</div>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <div class="bg-white rounded-lg shadow-sm border p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold"><?= htmlspecialchars($p['name']) ?></p>
                                <p class="text-xs text-gray-400"><?= htmlspecialchars($p['created_at']) ?></p>
                            </div>
                        </div>

                        <div class="mt-3 text-gray-800">
                            <?= nl2br(htmlspecialchars($p['content'])) ?>
                        </div>

                        <?php if (!empty($p['image'])): ?>
                            <div class="mt-3">
                                <img src="<?= htmlspecialchars($p['image']) ?>" class="w-full rounded-md object-cover max-h-96">
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../Views/layout.php';
