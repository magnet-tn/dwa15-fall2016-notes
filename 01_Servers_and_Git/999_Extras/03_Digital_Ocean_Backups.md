# Digital Ocean Backups

DigitalOcean has a feature called *Snapshots* where you can take a, well, snapshot of your Droplet in it's current state, including all configurations and data.

+ Snapshots cost $.05 per GB to store.
+ Snapshots can be created manually, or set to be created automatically once a week with DO's *Backup* feature (costs an additional $1/mo for the small plans we use)

In this course, **you are not required to take snapshots** because the core of your work is redundantly stored on your local computer, on Github.com, *and* on DigitalOcean.

However, **some students choose to periodically take Snapshots throughout the semester for peace of mind** because while your code is redundantly backed-up, your server configuration and data, such as database contents, is not.

For full details on backups/snapshots, see [DigitalOcean Backups and Snapshots Explained](https://www.digitalocean.com/community/tutorials/digitalocean-backups-and-snapshots-explained).

In &ldquo;real world&rdquo; applications it is *strongly* recommended that you have a backup system for your server.

## Taking a Snapshot
To take a Snapshot, you must first turn off your Droplet. This can be done while SSH'd into your server with this command:

```bash
$ sudo poweroff
```

This will turn off your droplet and exit you out of your SSH connection.

Example:

```xml
root@Feb4DynamicWebApplications:~# sudo poweroff
root@Feb4DynamicWebApplications:~#
Broadcast message from root@Feb4DynamicWebApplications
	(/dev/pts/0) at 21:59 ...

The system is going down for power off NOW!
Connection to 162.243.95.209 closed by remote host.
Connection to 162.243.95.209 closed.
```

Once your Droplet is powered off, via the *Snapshot* section in DigitalOcean, you can create a new Snapshot:

<img src='http://making-the-internet.s3.amazonaws.com/sysadmin-take-the-snapshot@2x.png' style='width:100%; max-width:1000px'>

How long it takes your Snapshot to complete will vary; in my tests it took anywhere from a few seconds to a few minutes. The more data you have on your server, the longer the Snapshot creation will take.

After DigitalOcean is done making the Snapshot you can power your server back on via the *Power* section in DigitalOcean.

<img src='http://making-the-internet.s3.amazonaws.com/sysadmin-power-on-do@2x.png' style='max-width:1186px; width:100%' alt=''>

## Restoring/Starting from a Snapshot
You can now restore from this Snapshot in one of two places:

1. Droplet settings -> *Snapshots* -> *Restore from Snapshot*
2. When creating a new Droplet, under *Images*, you can select a previously created snapshot ([screenshot](http://making-the-internet.s3.amazonaws.com/vc-creating-new-droplet-from-snapshot.png)).
