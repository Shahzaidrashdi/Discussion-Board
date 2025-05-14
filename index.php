<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .topic-card { transition: transform 0.2s; }
        .topic-card:hover { transform: translateY(-5px); }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Discussion Board</a>
            <div class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <a class="nav-link" href="create_topic.php">New Topic</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Discussion Topics</h1>
        
        <?php
        $stmt = $pdo->query("SELECT t.*, u.username 
                            FROM topics t 
                            JOIN users u ON t.user_id = u.id 
                            ORDER BY t.created_at DESC");
        $topics = $stmt->fetchAll();
        
        if (empty($topics)) {
            echo '<div class="alert alert-info">No topics yet. Be the first to start a discussion!</div>';
        } else {
            foreach ($topics as $topic) {
                $postCount = $pdo->query("SELECT COUNT(*) FROM posts WHERE topic_id = {$topic['id']}")->fetchColumn();
                echo '
                <div class="card topic-card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><a href="topic.php?id='.$topic['id'].'">'.$topic['title'].'</a></h5>
                        <p class="card-text text-muted">'.substr($topic['description'], 0, 150).'...</p>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Posted by '.$topic['username'].' on '.date('M j, Y', strtotime($topic['created_at'])).'</small>
                            <small class="text-muted">'.$postCount.' replies</small>
                        </div>
                    </div>
                </div>';
            }
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>