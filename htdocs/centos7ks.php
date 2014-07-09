<?php header("Content-type: text/plain"); ?>
# Kickstart for CentOS 7

install
url --url http://mirror.rackspace.com/CentOS/7/os/x86_64/
lang en_US.UTF-8
keyboard us
network --onboot=yes --device=eth0 --bootproto=dhcp --noipv6 --hostname=<?php echo $_GET["hostname"]; ?>

rootpw cH4N9EMe
firewall --service=ssh
authconfig --enableshadow --passalgo=sha512
selinux --disabled
timezone --utc America/Chicago
bootloader --location=mbr --driveorder=sda
clearpart --all
zerombr yes
part / --fstype=ext4 --size=500 --grow
part swap --fstype=swap --recommended
text
reboot

%packages --nobase
@core
-aic94xx-firmware
-atmel-firmware
-bfa-firmware
-ipw2100-firmware
-ipw2200-firmware
-ivtv-firmware
-iwl1000-firmware
-iwl3945-firmware
-iwl4965-firmware
-iwl5000-firmware
-iwl5150-firmware
-iwl6000-firmware
-iwl6050-firmware
-kernel-firmware
-libertas-usb8388-firmware
-ql2100-firmware
-ql2200-firmware
-ql23xx-firmware
-ql2400-firmware
-ql2500-firmware
-rt61pci-firmware
-rt73usb-firmware
-xorg-x11-drv-ati-firmware
-zd1211-firmware

vim-enhanced
openssh-server
openssh-clients
file
man
mlocate


%post
rpm -ivh http://mirror.rackspace.com/epel/beta/7/x86_64/epel-release-7-0.2.noarch.rpm
rpm -ivh http://yum.puppetlabs.com/puppetlabs-release-el-7.noarch.rpm

yum -y install puppet

%end

