<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;

class DashboardController extends Controller {
    public function index() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }
        $this->view('dashboard.php', ['user' => $user]);
    }
    public function search() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $q = trim($_GET['q'] ?? '');
        $type = trim($_GET['type'] ?? 'posts'); // default to posts
        $q = mb_substr($q, 0, 255); // limit length

        $results = [];
        if ($q !== '') {
            if ($type === 'users') {
                $results = \App\Models\User::searchByName($q);
            } else { // posts (default)
                $results = \App\Models\Post::searchByKeyword($q);
            }
        }

        $this->view('search/results.php', [
            'user' => $user,
            'q' => $q,
            'type' => $type,
            'results' => $results,
        ]);
    }

}
