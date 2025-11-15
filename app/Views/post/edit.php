<?php
$title = 'Edit Post | AuthBoard';
ob_start();
?>
    <div class="max-w-md mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Edit Post</h2>

        <form method="POST" action="/post/edit" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>" />
            <div>
                <label class="block text-sm font-medium text-gray-700">Content</label>
                <textarea name="content" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($post['content']) ?></textarea>
            </div>

            <?php if (!empty($post['image'])): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Image</label>
                    <img src="<?= htmlspecialchars($post['image']) ?>" alt="Current Image" class="mt-2 rounded-md max-h-48 object-cover w-full">
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700">Replace image (optional)</label>
                <input type="file" name="image" class="block w-full text-sm text-gray-700">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold">Save changes</button>
                <a href="/dashboard" class="px-4 py-2 rounded-md border">Cancel</a>
            </div>
        </form>
    </div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
