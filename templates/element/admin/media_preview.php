<?php
/** Preview the same SVG icon or image used by a public content card. */
$source = '';
$isIcon = false;
$icon = trim((string)($item['icon'] ?? ''));
if (preg_match('/^<svg\b[^>]*>/i', $icon, $openingTag)) {
    // SVGs copied from inline HTML may omit the namespace required in an image.
    if (!preg_match('/\bxmlns\s*=/i', $openingTag[0])) {
        $icon = preg_replace('/^<svg\b/i', '<svg xmlns="http://www.w3.org/2000/svg"', $icon, 1);
    }
    // An image document displays the icon without executing pasted inline markup.
    $source = 'data:image/svg+xml;base64,' . base64_encode($icon);
    $isIcon = true;
} else {
    $filename = trim((string)($item['img'] ?? ''));
    if ($filename !== '' && !preg_match('~[/\\\\]~', $filename)) {
        foreach (['thumbs/', ''] as $variant) {
            $relative = 'img/' . $directory . '/' . $variant . $filename;
            if (is_file(WWW_ROOT . $relative)) {
                $source = '/img/' . $directory . '/' . $variant . rawurlencode($filename);
                break;
            }
        }
    }
}
?>
<?php if ($source !== ''): ?>
<img class="admin-media-preview" src="<?= h($source) ?>" width="<?= $isIcon ? 48 : 150 ?>" style="max-width:150px;max-height:100px;object-fit:contain" alt="">
<?php else: ?>
<span class="text-muted">Нет изображения</span>
<?php endif; ?>
