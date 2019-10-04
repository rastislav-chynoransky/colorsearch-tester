<section>
    <form method="get" novalidate>
        <?php foreach ($colors as $color => $amount): ?>
        <div>
            <input type="color" name="colors[]" value="<?php echo e($color->toHex()); ?>">
            <input type="number" name="amounts[]" step="0.1" min="0" max="1" value="<?php echo e($amount); ?>">
            <a href="#" onclick="this.parentNode.remove();">&times;</a>
        </div>
        <?php endforeach; ?>
        <?php if (reset($colors)): ?>
        <button type="submit">&#x1f50d;</button>
        <?php endif; ?>
    </form>
</section>
