<html>
<head><title>Diego Instance Identity Tester</title>
<style>
p,h1,h2,h3 {
    font-family: Arial,sans-serif;
}
</style>
</head>
<body>
<h1>Diego Instance Identity Toolkit</h1>
<p>A toolkit for Diego's experimental <a href="https://github.com/cloudfoundry/diego-release/blob/develop/docs/instance-identity.md">Instance Identity</a> feature</P
<h2>Cloud Foundry Environment Variables</h2>

<?php

class sslcert
{
    public $common_name;
    public $org_unit;
    public $san;
    public $validfrom;
    public $validto;

    public function __construct ($pem) {
        $this->userName = $UserName;
        $ssl = openssl_x509_parse($pem);
        $this->common_name = $ssl['subject']['CN'];
        $this->org_unit = 'NA';
        if (array_key_exists('OU',$ssl['subject']))
            $this->org_unit = $ssl['subject']['OU'];
    
        $this->san = 'NA';
        if (array_key_exists('extensions',$ssl))
            if (array_key_exists('subjectAltName',$ssl['extensions']))
                $this->san = $ssl['extensions']['subjectAltName'];

        $this->validfrom = date_create_from_format('ymdHise', $ssl['validFrom'])->format('c');
        $this->validto = date_create_from_format('ymdHise', $ssl['validTo'])->format('c');
    } 
}

$cf_instance_guid = 'NA';
if (getenv('CF_INSTANCE_GUID'))
    $cf_instance_guid = getenv('CF_INSTANCE_GUID');

$cf_instance_key = 'NA';
if (getenv('CF_INSTANCE_KEY'))
    $cf_instance_key = getenv('CF_INSTANCE_KEY');

$cf_instance_cert = 'NA';
if (getenv('CF_INSTANCE_CERT'))
    $cf_instance_cert = getenv('CF_INSTANCE_CERT');

?>

<table border="1">
<tr><td>CF_INSTANCE_GUID</td><td><?php echo $cf_instance_guid;?></td></tr>
<tr><td>CF_INSTANCE_KEY</td><td><?php echo $cf_instance_key;?></td></tr>
<tr><td>CF_INSTANCE_CERT</td><td><?php echo $cf_instance_cert;?></td></tr>
</table>

<?php
$certarray;
if (getenv('CF_INSTANCE_CERT')) {

    $cert = file_get_contents($cf_instance_cert);
    $cur_pos = 0;
    $next_pos = strpos($cert, '-----END CERTIFICATE-----', $cur_pos);
    $i = 1;

    if ($next_pos > 0)
        echo "<h2>Certificates in $cf_instance_cert</h2>";

    while ($next_pos > 0) {
        $nextcert = substr($cert, $cur_pos, $next_pos + strlen('-----END CERTIFICATE-----') + 1);
        $certobj = new sslcert($nextcert);

$html = <<<HTML
<h3>Certificate $i</h3>
<table border="1">
<tr><td>Common Name</td><td>$certobj->common_name</td></tr>
<tr><td>Organizational Unit</td><td>$certobj->org_unit</td></tr>
<tr><td>SAN</td><td>$certobj->san</td></tr>
<tr><td>Valid From</td><td>$certobj->validfrom</td></tr>
<tr><td>Valid To</td><td>$certobj->validto</td></tr>
</table>
HTML;

        echo $html;
        $cur_pos = $next_pos + strlen('-----END CERTIFICATE-----') + 1;
        $next_pos = strpos($cert, '-----END CERTIFICATE-----', $cur_pos);
        $i++;
        }

$html = <<<HTML
<h2>Raw Contents of CRT file</h2>
<textarea rows="40" cols="90">
$cert
</textarea>
HTML;
    
    echo $html;

    }

?>

</body></html>
