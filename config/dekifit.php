<?php

return [

    // Where order and contact-form notifications go (the coach's inbox).
    'admin_email' => env('ADMIN_EMAIL', 'badzukov.d@hotmail.com'),

    'whatsapp' => '+389 71 271 390',
    'whatsapp_url' => 'https://wa.me/38971271390',
    'instagram_url' => 'https://www.instagram.com/ddbadzukov3_',
    'facebook_url' => 'https://www.facebook.com/DejanBadzukov',

    // Same account as resources/js/data/bank.js; keep the two in sync.
    'bank' => [
        'beneficiary' => 'Dejan Badjukov',
        'address' => 'Bul. Marshal Tito 5/4-7, Strumica, North Macedonia',
        'iban' => 'MK07240117372707105',
        'bic' => 'UIBMMK22XXX',
        'bank_name' => 'Universal Investment Bank AD Skopje (UNIBANK)',
        'bank_address' => 'Maksim Gorki 6, 1000 Skopje, North Macedonia',
    ],

];
