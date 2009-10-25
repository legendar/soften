<?

def(Array(
    'SITE_NAME' => 'Your site name',

    'SITE_ENCODING' => 'utf-8',
    'SESSION_NAME' => 'soften_session',
    
    'USER_SESSION_KEY' => 'user',
    'USER_LEVEL_KEY' => 'levelnum',
    
    'SL_GUEST' => 0,
    'SL_USER' => 100,
    'SL_ADMIN' => 500,
    'SL_SUPERADMIN' => 999,
    
    'INVALID_LOGINS_COUNT' => 3,
    'INVALID_LOGINS_TIMEOUT' => 60,
    
    'SITE_LNG' => 'english',
    
    'SITE_EXPIRE' => 30 * 60 // 30 minutes
));

def('SITE_EXPIRES_HEADER', gmdate('D, d M Y H:i:s', time() + SITE_EXPIRE) . ' GMT');
def('SITE_EXPIRES_NOCACHE', 'Mon, 26 Jul 1997 05:00:00 GMT');
def('SITE_EXPIRES_NOW', gmdate('D, d M Y H:i:s') . ' GMT');

