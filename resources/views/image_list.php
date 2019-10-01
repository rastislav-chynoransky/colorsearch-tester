<?php foreach ($images as $image): ?>
    <?php foreach ($image['colors'] as $color): ?>
        <a
            style="height: 100px; width: 100px; display: inline-block; background-color: <?php echo $color['hex'] ?? ''; ?>"
            href="<?php echo url(sprintf('/?color=%s', urlencode($color['hex']))); ?>">
        </a>
    <?php endforeach; ?>
<?php endforeach; ?>
