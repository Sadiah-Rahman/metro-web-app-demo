<?php
$title = 'Edit Profile | AuthBoard';
ob_start();
?>

    <div class="max-w-md mx-auto">
        <h2 class="text-2xl font-semibold mb-4 text-blue-700">Edit Profile</h2>

        <form method="POST" action="/user/edit" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>" required class="w-full border border-gray-300 rounded-md p-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required class="w-full border border-gray-300 rounded-md p-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea name="bio" class="w-full border border-gray-300 rounded-md p-2"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Avatar (optional)</label>
                <input type="file" name="avatar" class="block w-full text-sm text-gray-700" />
                <?php if (!empty($profile['avatar'])): ?>
                    <img src="<?= htmlspecialchars($profile['avatar']) ?>" class="mt-3 w-20 h-20 rounded-full object-cover">
                <?php endif; ?>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Save</button>
                <a href="/user?id=<?= htmlspecialchars($profile['id']) ?>" class="px-4 py-2 border rounded-md">Cancel</a>
            </div>
        </form>
    </div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../Views/layout.php';
