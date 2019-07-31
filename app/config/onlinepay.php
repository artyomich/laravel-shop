<?php return [
	'methods' => [
        '\modules\onlinepay\controllers\CashController' => [],
        '\modules\onlinepay\controllers\GazpromController' => [
            'default' => [
                'merch_id' => '1DB23FCBC480B29E8C2489A557665BAA',
                'account_id' => '653A2E37AA855A08C061B5AED24FC0D6',
            ],
            'novokuznetsk' => [
                'merch_id' => '7A66B25E08D72C36B0AE646441EECDAB',
                'account_id' => 'DA7F7B9FD342CBBDB6796B6F0490BEDF',
            ],
            'nizhny_novgorod' => [
                'merch_id' => '41F320984E5FB1F7C38FA8971BBC1AEB',
                'account_id' => '86709A70207F6F1BFE9215EF3B7843C5',
            ],
            'krasnoyarsk' => [
                'merch_id' => '4FBA50B5B642E437C1E41252EAF285A0',
                'account_id' => '5291A634DB70AC60E58A56A2021A350A',
            ],
            'belgorod' => [
                'merch_id' => '2BD0A643062498FD58B5A80050B272B0',
                'account_id' => 'FA1FE73236DF46A6927D055CF77CA869',
            ],
            'chelyabinsk' => [
                'merch_id' => '4C0B5791BFAAC966C8BC9933C441BE9D',
                'account_id' => '08A1AD5D6E753C81088BDFA5BC840F31',
            ],
        ],
        '\modules\onlinepay\controllers\BillController' => [],
	],
	'prepayCities' => ['moscow', 'nizhny_novgorod']
];