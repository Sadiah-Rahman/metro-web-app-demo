<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;

class PostController extends Controller {
    public function create() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        $imagePath = null;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $filename = time() . '_' . basename($_FILES['image']['name']);
            $target = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imagePath = '/uploads/' . $filename;
            }
        }

        if ($content || $imagePath) {
            Post::create($user['id'], $content, $imagePath);
        }

        header('Location: /dashboard');
    }
}

