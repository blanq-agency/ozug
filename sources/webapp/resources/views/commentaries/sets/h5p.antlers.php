<div class="h5p-container" style="position: relative; width: 100%; max-width: 960px; margin: 0 auto;">
    <?php
    if (!empty($set['h5p_url'])) {
        $url = $set['h5p_url'];
        echo '<iframe src="' . htmlspecialchars($url) . '" width="959" height="453" frameborder="0" allowfullscreen title="H5P-Element"></iframe>';
        echo '<script src="https://openrewi.org/wp-content/plugins/h5p/h5p-php-library/js/h5p-resizer.js" charset="UTF-8"></script>';
    } else {
        echo '<p>Kein H5P-Element vorhanden.</p>';
    }
    ?>
</div>
