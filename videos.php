<?php
// videos.php
$videoDir = 'uploads/';
$videoTypes = ['mp4', 'webm', 'ogg']; // Allowed video formats

if (!is_dir($videoDir)) {
    echo "<p>No video directory found.</p>";
    exit;
}

$videos = [];
foreach ($videoTypes as $type) {
    foreach (glob($videoDir . "*.$type") as $file) {
        $videos[] = $file;
    }
}

if (empty($videos)) {
    echo "<p>No project videos uploaded yet.</p>";
} else {
    foreach ($videos as $video) {
        $filename = basename($video);
        echo '<div class="project-video">';
        echo '<video width="320" height="240" controls>';
        echo '<source src="'.$video.'" type="video/'.pathinfo($video, PATHINFO_EXTENSION).'">';
        echo 'Your browser does not support the video tag.';
        echo '</video>';
        echo '<p>' . htmlspecialchars($filename) . '</p>';
        echo '</div>';
    }
}
?>
