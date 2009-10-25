<?

def('MAIL_TYPE', 'smtp');
def('MAIL_PORT', '25');
//def('MAIL_HOST',             'luckyteam.co.uk');
def('MAIL_HOST',             '87.124.70.52');
def('MAIL_AUTH',             true);
def('MAIL_AUTH_USER',        'cv');
def('MAIL_AUTH_PASSWORD',    'cv'); 

/* Mail notifications
 *   0 - no email notifications, 
 *   1 - standard email notifications, 
 *   2 - all notifications send on EMAIL_NOTIFICATION_EMAILS
 */
def('EMAIL_NOTIFICATIONS', '2'); 
def('EMAIL_NOTIFICATION_EMAILS', 'legendar@gmail.com');

?>