<?php
namespace App\Service;

class PaymentPlans
{
    /** Match by payment terms, never by the card's display position. */
    public static function names(iterable $services): array
    {
        $names = ['5050' => 'Рассрочка 50/50', '3070' => 'Рассрочка 30/70', 'full' => '100% оплата'];
        foreach ($services as $service) {
            $name = trim((string)($service['name'] ?? ''));
            $terms = $name . ' ' . ($service['motif'] ?? '');
            if ($name === '') {
                continue;
            }
            if (preg_match('#50\s*[/−–-]\s*50#u', $terms)) {
                $names['5050'] = $name;
            } elseif (preg_match('#30\s*[/−–-]\s*70#u', $terms)) {
                $names['3070'] = $name;
            } elseif (preg_match('/100\s*%/u', $terms)) {
                $names['full'] = $name;
            }
        }
        return $names;
    }
}
