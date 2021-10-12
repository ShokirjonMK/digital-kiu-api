<?php

use common\models\StorageBrowser;

if ($files) : ?>
    <?php foreach ($files as $file) : ?>
        <?php $image_preview = StorageBrowser::checkImagePreview($file); ?>
        <div class="storage-browser-grid-item storage-browser-grid-file">
            <div class="storage-browser-grid-item-in">
                <div class="storage-browser-grid-item-top">
                    <i class="ri-checkbox-blank-line storage-browser-select-icon" storage-browser-select="<?= $file['name']; ?>" storage-browser-file-url="<?= $file['file_url']; ?>" storage-browser-select-item="file"></i>

                    <?php if ($image_preview) : ?>
                        <div class="storage-browser-grid-image">
                            <img src="<?= $image_preview; ?>" alt="<?= $file['name']; ?>">
                        </div>
                    <?php else : ?>
                        <div class="storage-browser-grid-icon">
                            <i class="<?= StorageBrowser::iconConvert($file); ?>"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="storage-browser-grid-item-bottom" storage-browser-info="<?= $file['name']; ?>">
                    <i class="ri-information-fill mr-2" data-toggle="tooltip" data-placement="top" title="<?= _e('Informations'); ?>"></i>
                    <span title="<?= $file['name']; ?>"><?= $file['name']; ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>