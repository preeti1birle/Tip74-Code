<?php 
    const VERSION = 4.5;
    header('X-Frame-Options: sameorigin');
    $a = $_SERVER['REQUEST_URI']; 
    $a = str_replace("/tip74-dev/",'',$a);
    if(strpos($a, '?') !== false){
        $a = substr($a, 0, strpos($a, "?"));
    }
    $data = array_values(array_filter(explode('/',$a)));
    $PathName = '';
    if(count($data) > 0){
        $PathName = $data[count($data)-1];
    }
    $ServerName = $_SERVER['SERVER_NAME'];
    switch ($_SERVER['SERVER_NAME']) {
        case 'localhost':
         $base_url = 'http://localhost/tip74-dev/';
        //$base_url = 'http://*****/tip74-dev/';
        // $api_url = 'http://localhost/tip74-dev/api/';
        $api_url = 'http://*****/tip74-dev/api/';
        break;
        case '*****':
        $base_url = 'http://*****/tip74-dev/';
        $api_url = 'http://*****/tip74-dev/api/';
        break;
        case 'staging11.tip74.com':
        $base_url = 'http://staging11.tip74.com/';
        $api_url = 'http://staging11.tip74.com/api/';
        break;  
        default :
        $_SERVER['CI_ENV'] = 'production';
        $base_url = 'https://www.tip74.com/';
        $api_url = 'https://www.tip74.com/api/';
        break;
    }
?>
<?php echo "<title>Tip74</title>"; ?>