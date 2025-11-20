<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class UserController extends Controller {
    public function profile() {
        $current = Session::get('user'); // may be null
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Invalid user id.";
            return;
        }

        $user = User::findById($id);
        if (!$user) {
            http_response_code(404);
            echo "User not found.";
            return;
        }

        $posts = User::postsByUser($id);
        $followers = User::followerCount($id);
        $isFollowing = false;
        if (!empty($current)) {
            $isFollowing = User::isFollowing((int)$current['id'], $id);
        }

        $this->view('user/profile.php', [
            'profile' => $user,
            'posts' => $posts,
            'followers' => $followers,
            'isFollowing' => $isFollowing,
            'user' => $current, // pass current session user for layout/actions
        ]);
    }

    public function follow() {
        $current = Session::get('user');
        if (!$current) {
            header('Location: /login');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Invalid user id.";
            return;
        }

        \App\Models\User::follow((int)$current['id'], $id);
        header('Location: /user?id=' . $id);
    }

    public function unfollow() {
        $current = Session::get('user');
        if (!$current) {
            header('Location: /login');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Invalid user id.";
            return;
        }

        \App\Models\User::unfollow((int)$current['id'], $id);
        header('Location: /user?id=' . $id);
    }
    // inside App\Controllers\UserController

    public function editForm() {
        $current = Session::get('user');
        if (!$current) {
            header('Location: /login');
            exit;
        }

        $profile = \App\Models\User::findById((int)$current['id']);
        if (!$profile) {
            http_response_code(404);
            echo "Profile not found.";
            return;
        }

        // Pass current user as $user for layout too
        $this->view('user/edit.php', [
            'profile' => $profile,
            'user' => $current,
        ]);
    }

    public function updateProfile() {
        $current = Session::get('user');
        if (!$current) {
            header('Location: /login');
            exit;
        }

        $id = (int)$current['id'];
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '') ?: null;

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("updateProfile: invalid name/email for user {$id}");
            echo "Invalid name or email.";
            return;
        }

        $avatarPath = null;

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
            finfo_close($finfo);

            $allowedMimes = ['image/png','image/jpeg','image/gif','image/webp'];
            if (!in_array($mime, $allowedMimes, true)) {
                error_log("updateProfile: avatar mime not allowed: $mime");
                echo "Invalid avatar file type.";
                return;
            }

            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $ext = $ext ? '.' . strtolower($ext) : '';

            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                error_log("updateProfile: failed to create upload dir: $uploadDir");
                echo "Server error.";
                return;
            }

            $filename = 'avatar_' . $id . '_' . time() . $ext;
            $target = $uploadDir . $filename;

            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                error_log("updateProfile: move_uploaded_file failed for user {$id}");
                echo "Failed to upload avatar.";
                return;
            }

            // Set web path (ensure it matches how you reference images elsewhere)
            $avatarPath = '/uploads/avatars/' . $filename;
        }

        // Update DB
        try {
            $updated = \App\Models\User::updateProfile($id, $name, $email, $bio, $avatarPath);
        } catch (\Throwable $e) {
            error_log("updateProfile: DB error for user {$id}: " . $e->getMessage());
            echo "Server error updating profile.";
            return;
        }

        if (!$updated) {
            error_log("updateProfile: updateProfile() returned false for user {$id}");
            echo "No changes saved.";
            return;
        }

        // Refresh session data from DB so header/layout shows updated values
        $fresh = \App\Models\User::findById($id);
        if ($fresh) {
            // keep only safe keys in session (id, name, email, avatar)
            Session::set('user', [
                'id' => $fresh['id'],
                'name' => $fresh['name'],
                'email' => $fresh['email'],
                'avatar' => $fresh['avatar'] ?? null
            ]);
        } else {
            error_log("updateProfile: could not re-fetch user {$id} after update");
        }

        // Redirect back to profile
        header('Location: /user?id=' . $id);
        exit;
    }


}
