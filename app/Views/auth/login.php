<?php
$title = 'Login | AuthBoard';
ob_start();
?>
    <div class="max-w-md mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Login</h2>
        <form method="POST" action="/login" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500" />
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md font-semibold">Login</button>
        </form>
        <p class="text-center mt-4 text-sm text-gray-600">
            Don’t have an account? <a href="/register" class="text-blue-600 hover:underline">Register</a>
        </p>
    </div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
