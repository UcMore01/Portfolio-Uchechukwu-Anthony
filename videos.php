<?php
$videoDir = 'uploads/';
$videoTypes = ['mp4', 'webm', 'ogg'];

if (!is_dir($videoDir)) {
    echo "<p>No videos found.</p>";
    return;
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
        $filename = htmlspecialchars(basename($video));
        echo '<div class="project-video">';
        echo '<video controls>';
        echo '<source src="'.$video.'" type="video/'.pathinfo($video, PATHINFO_EXTENSION).'">';
        echo 'Your browser does not support the video tag.';
        echo '</video>';
        echo "<p>$filename</p>";
        echo '</div>';
    }
}
?>
