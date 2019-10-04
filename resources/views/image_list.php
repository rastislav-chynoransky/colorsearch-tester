<span class="rainbow">&#x1f308;</span>

<section>
    <?php foreach ($images as $image): ?>
    <span class="colors-container <?php echo app('request')->query('id', -1) == $image->id ? 'active' : '' ?>">
        <a href="/?id=<?php echo e($image->id); ?>">
            <img src="<?php echo e($image->getUrl()) ?>">
            <?php foreach ($image->getColorDistribution() as $color => $amount): ?>
            <span style="background-color: <?php echo e($color->toHex()); ?>; width: 100px; height: <?php echo e($amount * 100); ?>%"></span>
            <?php endforeach; ?>
        </a>
    </span>
    <?php endforeach; ?>
</section>

<style>
    .colors-container {
        display: inline-block;
        height: 100px;
        position: relative;
        width: 100px;
    }

    .colors-container.active {
        box-shadow: black 0 0 10px;
        animation: highlight .5s infinite;
    }

    .colors-container > a {
        display: block;
        height: 100%;
        position: relative;
        width: 100%;
    }

    .colors-container > a > span {
        display: block;
    }

    .colors-container > a > img {
        display: none;
        height: 100px;
        position: absolute;
        top: 0;
        width: 100px;
    }

    .colors-container:hover {
        animation: none;
    }

    @keyframes highlight {
        from {
            transform: translate(0);
        }
        50% {
            transform: translate(0, -5px);
        }
        to {
            transform: translate(0);
        }
    }

    .rainbow {
        cursor: pointer;
    }

    .colors-container > a:hover img, .rainbow:hover + * img {
        display: block;
    }
</style>
