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

    // GET /post/edit?id=123
    public function editForm() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        $post = Post::find($id);
        if (!$post) {
            http_response_code(404);
            echo "Post not found.";
            return;
        }

        // Only owner allowed
        if ($post['user_id'] != $user['id']) {
            http_response_code(403);
            echo "Forbidden.";
            return;
        }

        // Check 24-hour window
        $created = new \DateTime($post['created_at']);
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $created->getTimestamp();
        if ($interval > 24 * 60 * 60) {
            echo "Edit window (24 hours) has passed.";
            return;
        }

        $this->view('post/edit.php', ['post' => $post]);
    }

    // POST /post/edit
    public function edit() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $post = Post::find($id);
        if (!$post) {
            http_response_code(404);
            echo "Post not found.";
            return;
        }

        if ($post['user_id'] != $user['id']) {
            http_response_code(403);
            echo "Forbidden.";
            return;
        }

        // Check 24-hour window
        $created = new \DateTime($post['created_at']);
        $now = new \DateTime();
        $interval = $now->getTimestamp() - $created->getTimestamp();
        if ($interval > 24 * 60 * 60) {
            echo "Edit window (24 hours) has passed.";
            return;
        }

        $content = trim($_POST['content'] ?? '');
        $imagePath = null;

        // Handle new image upload (optional)
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $filename = time() . '_' . basename($_FILES['image']['name']);
            $target = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imagePath = '/uploads/' . $filename;
            }
        }

        // If no new image and content empty, keep old image
        if ($imagePath === null) {
            $imagePath = $post['image'] ?? null;
        }

        Post::update($id, $content, $imagePath);

        header('Location: /dashboard');
    }

    // GET /post/delete?id=123  (we'll also check POST for better safety)
    // POST /post/delete
    public function delete() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        // Support both GET(id) or POST(id)
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $post = Post::find($id);
        if (!$post) {
            http_response_code(404);
            echo "Post not found.";
            return;
        }

        if ($post['user_id'] != $user['id']) {
            http_response_code(403);
            echo "Forbidden.";
            return;
        }

        Post::delete($id);

        header('Location: /dashboard');
    }
}
