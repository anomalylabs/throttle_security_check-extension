<?php

return [
    'max_attempts'      => [
        'label'        => 'İzin Verilen Oturum Açma Girişimleri',
        'instructions' => '<strong>Throttle Interval</strong> içinde kaç tane başarısız oturum açma girişimine izin verilir?',
    ],
    'throttle_interval' => [
        'label'        => 'Throttle Interval',
        'instructions' => 'Belirtilen dakika sayısı içinde <strong>İzin Verilen Giriş Denemelerine</strong> ulaşılırsa, kullanıcıyı kilitleyin.',
    ],
    'lockout_interval'  => [
        'label'        => 'Kilitleme Aralığı',
        'instructions' => 'Kısıtlanmış bir kullanıcının tekrar oturum açmayı denemeden önce kaç dakika kilitli kalacağını belirtin.',
    ],
];
