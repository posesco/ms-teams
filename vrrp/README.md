
## What is VRRP

VRRP (Virtual Router Redundancy Protocol) is a commonly used protocol that offers high availability for a network (or subnetwork). Keepalived is a Linux package that uses VRRP to deliver high availability among Linux servers.

Keepalived should be available through most Linux repositories, so use the appropriate package manager to your distribution to install it on each device that will be running the service.

## Prerequisites

* Linux installation on at least 2 hosts (be sure they are already updated).

* At least 3 available IP addresses (1 for each of at least 2 peer keepalived servers, and 1 virtual IP shared amongst them).

## How to use this script

You can paste in the following configuration block, though you’ll want to make the appropriate changes for your environment (including IPs and interfaces).

```bash
sudo vim /etc/keepalived/keepalived.conf 
sudo vim /etc/keepalived/floating.sh 
```


