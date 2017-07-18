# Instance Identity Toolkit
Tools to test and work with Diego Instance Identity.  

## 1. Introduction  
From the [Diego Instance Identity](https://github.com/cloudfoundry/diego-release/blob/develop/docs/instance-identity.md) page:   
_The instance identity system in Diego provides each application instance with a PEM-encoded X.509 certificate and PKCS#1 RSA private key._  
  
This page gives you a script to generate a known, good private key and certificate for your CA. It then gives you a PHP application to display the brand new container-specific certificate that Diego has just made for you!  

## 2. Prerequisites  
1. You must have access to latest installation of Cloud Foundry (not PCF Dev), including access to BOSH.  
2. You must have access to a workstation with CF CLI.  
3. You have cloned this repository to your workstation and are logged into a terminal.  

## 3. Generating the  CA Keys  
Run [createCA.sh](https://github.com/bendalby82/instance-identity-toolkit/blob/master/createCA.sh) from a Linux or OSX terminal that has openssl installed.  

The script will generate a subfolder called `ca` with the following two files:  
```
ca-cert.pem 
ca-private-key
```
## 4. Applying the keys to your Cloud Foundry Deployment Manifest  
As per the [instructions](https://github.com/cloudfoundry/diego-release/blob/develop/docs/instance-identity.md), you need to copy the contents of the two PEM files above into your Cloud Foundry deployment manifest, and do a `bosh deploy` when you have completed the edit.  

When you have finished the edit, your Cloud Foundry deployment manifest should look something like the following extract (certificate contents deleted):  
```
  properties:
    diego:
      executor:
        disk_capacity_mb: 
        memory_capacity_mb: 
        post_setup_hook: sh -c "rm -f /home/vcap/app/.java-buildpack.log /home/vcap/app/**/.java-buildpack.log"
        post_setup_user: root
        ca_certs_for_downloads: |
          -----BEGIN CERTIFICATE-----
          -----END CERTIFICATE-----
        instance_identity_ca_cert: |
          -----BEGIN CERTIFICATE-----
          -----END CERTIFICATE-----
        instance_identity_key: |
          -----BEGIN RSA PRIVATE KEY-----
          -----END RSA PRIVATE KEY-----
      rep:
        require_tls: true
        enable_legacy_api_endpoints: false
        ca_cert: |

```

## 5. Inspecting the Results  
Now you've done the hard bit, it is time to inspect the results.  
  
Just go the [certapp](https://github.com/bendalby82/instance-identity-toolkit/tree/master/certapp) folder and do a cf push:  
```
cf push testmycerts
```
If all goes well, you should see something like the following:  
![screenshot](https://github.com/bendalby82/instance-identity-toolkit/blob/master/images/toolkit-screenshot.png)  

The tool extracts certificate start and end times deliberately, so that you can watch Diego rotate the certificates as the expiry time grows near. The default certificate lifetime is set to 24 hours, so unless you adjust `diego.executor.instance_identity_validity_period_in_hours`, this may be something you have to leave overnight.  

## 6. Licensing

The Toolkit is freely distributed under the [MIT License](https://opensource.org/licenses/MIT). See LICENSE for details.

## 7. Contribution

Create a fork of the project into your own reposity. Make all your necessary changes and create a pull request with a description on what was added or removed and details explaining the changes in lines of code. If approved, project owners will merge it.

## 8. Support

Please file bugs and issues on the Github issues page for this project. This is to help keep track and document everything related to this repo. The code and documentation are released with no warranties or SLAs and are intended to be supported through a community driven process.
