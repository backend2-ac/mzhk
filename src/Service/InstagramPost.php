<?php
namespace App\Service;

class InstagramPost
{
    // Initial selection from the company's public profile. CMS values take precedence.
    private const DEFAULT_POSTS = [
        'social_post_1' => 'https://www.instagram.com/reel/Dewohcrq2qE/',
        'social_post_2' => 'https://www.instagram.com/reel/DerZe_dqypr/',
        'social_post_3' => 'https://www.instagram.com/reel/DehSV_jqD0n/',
        'social_post_4' => 'https://www.instagram.com/reel/Deb9ckiKVLa/',
    ];

    public static function forSlot(string $alias, string $body): array
    {
        $post = self::read($body);
        $data = json_decode($body, true);
        if (!is_array($data) || ($data['_type'] ?? '') !== 'instagram-post') {
            $post['url'] = self::DEFAULT_POSTS[$alias] ?? '';
        }
        return $post;
    }

    public static function read(string $body): array
    {
        $data = json_decode($body, true);
        if (is_array($data) && ($data['_type'] ?? '') === 'instagram-post') {
            try {
                $url = self::url((string)($data['url'] ?? ''));
            } catch (\InvalidArgumentException $error) {
                $url = '';
            }
            return ['url' => $url, 'caption' => (string)($data['caption'] ?? '')];
        }
        return ['url' => '', 'caption' => $body];
    }

    /** Accept a public post URL or Instagram's copied embed code; never render submitted HTML. */
    public static function url(string $input): string
    {
        $input = trim(html_entity_decode($input, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($input === '') {
            return '';
        }
        if (strlen($input) > 50000) {
            throw new \InvalidArgumentException('Код Instagram слишком длинный. Вставьте ссылку на публикацию.');
        }
        if (strpos($input, '<') !== false) {
            if (!preg_match('/data-instgrm-permalink\s*=\s*([\'"])(.*?)\1/is', $input, $match)) {
                throw new \InvalidArgumentException('Вставьте ссылку на публикацию Instagram или её код «Встроить».');
            }
            $input = $match[2];
        }
        $parts = parse_url($input);
        if (!$parts || strtolower($parts['scheme'] ?? '') !== 'https'
            || !in_array(strtolower($parts['host'] ?? ''), ['instagram.com', 'www.instagram.com'], true)
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])
            || !preg_match('#^/(?:[A-Za-z0-9_.]+/)?(p|reel|tv)/([A-Za-z0-9_-]+)/?(?:embed/?)?$#D', $parts['path'] ?? '', $match)) {
            throw new \InvalidArgumentException('Нужна HTTPS-ссылка на конкретную публикацию или Reel в Instagram.');
        }
        return 'https://www.instagram.com/' . $match[1] . '/' . $match[2] . '/';
    }

    public static function write(string $input, string $caption): string
    {
        return json_encode(['_type' => 'instagram-post', 'url' => self::url($input), 'caption' => trim($caption)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
