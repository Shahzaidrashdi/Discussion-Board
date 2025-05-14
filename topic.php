<?php 
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$topicId = (int)$_GET['id'];

// Get topic info
$stmt = $pdo->prepare("SELECT t.*, u.username 
                      FROM topics t 
                      JOIN users u ON t.user_id = u.id 
                      WHERE t.id = ?");
$stmt->execute([$topicId]);
$topic = $stmt->fetch();

if (!$topic) {
    header("Location: index.php");
    exit;
}

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && isset($_POST['content'])) {
    $content = sanitize($_POST['content']);
    
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO posts (content, user_id, topic_id) VALUES (?, ?, ?)");
        $stmt->execute([$content, $_SESSION['user_id'], $topicId]);
        header("Location: topic.php?id=$topicId");
        exit;
    }
}

// Get all posts for this topic
$stmt = $pdo->prepare("SELECT p.*, u.username 
                      FROM posts p 
                      JOIN users u ON p.user_id = u.id 
                      WHERE p.topic_id = ? 
                      ORDER BY p.created_at ASC");
$stmt->execute([$topicId]);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $topic['title'] ?> - Discussion Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $topic['title'] ?></li>
            </ol>
        </nav>
        
        <div class="card mb-4">
            <div class="card-body">
                <h2><?= $topic['title'] ?></h2>
                <p><?= nl2br($topic['description']) ?></p>
                <small class="text-muted">Posted by <?= $topic['username'] ?> on <?= date('M j, Y H:i', strtotime($topic['created_at'])) ?></small>
            </div>
        </div>
        
        <h3 class="mb-3">Replies (<?= count($posts) ?>)</h3>
        
        <?php if (empty($posts)): ?>
            <div class="alert alert-info">No replies yet. Be the first to respond!</div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p><?= nl2br($post['content']) ?></p>
                        <small class="text-muted">Posted by <?= $post['username'] ?> on <?= date('M j, Y H:i', strtotime($post['created_at'])) ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (isLoggedIn()): ?>
            <div class="card mt-4">
                <div class="card-body">
                    <h4>Post a Reply</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Reply</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">You must <a href="login.php">login</a> to post a reply.</div>
        <?php endif; ?>
    </div>
</body>
</html>